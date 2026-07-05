<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\BuyAssetRequest;
use App\Http\Requests\SadadCallbackRequest;
use App\Models\Order;
use App\Models\User;
use App\Models\Variable;
use App\Notifications\TransactionNotification;
use App\Services\ReferralService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    /**
     * Store a new order for buying an asset.
     */
    public function store(BuyAssetRequest $request): JsonResponse
    {
        $this->authorize('buyFromStore', User::class);

        $rate = Variable::getRate($request->asset);

        $user = $request->user();

        $order = Order::create([
            'user_id' => $user->id,
            'asset' => $request->asset,
            'amount' => $request->amount,
        ]);

        $transaction = $order->transaction()->create([
            'user_id' => $user->id,
            'asset' => $order->asset,
            'amount' => $order->amount,
            'action' => 'deposit',
        ]);

        $response = sadad()
            ->orderId($order->id)
            ->amount($order->amount * $rate)
            ->asset($order->asset)
            ->request()
            ->callbackUrl(route('order.callback'))
            ->send();

        if (! $response->success()) {
            $gatewayError = $response->error();

            throw ValidationException::withMessages([
                'code' => (string) $gatewayError->code(),
                'error' => $gatewayError->message(),
            ]);
        }

        $transaction->update(['token' => $response->token()]);

        return response()->json([
            'link' => $response->url(),
        ]);
    }

    /**
     * Handle the callback after a payment is made.
     */
    public function callback(SadadCallbackRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $resCode = (int) $validated['ResCode'];

        return DB::transaction(function () use ($validated, $resCode, $request) {
            $order = Order::where('id', $validated['OrderId'])
                ->with('user', 'transaction', 'payment')
                ->lockForUpdate()
                ->firstOrFail();

            $transaction = $order->transaction;

            if ($transaction === null) {
                Log::warning('Sadad callback received for order without transaction.', [
                    'order_id' => $order->id,
                ]);

                return $this->redirectToFrontend($validated, $resCode);
            }

            if ($order->payment !== null) {
                return $this->redirectToFrontend($validated, $resCode);
            }

            $storedToken = (string) $transaction->token;

            if ($storedToken === '' || ! hash_equals($storedToken, $validated['Token'])) {
                Log::warning('Sadad callback token mismatch.', [
                    'order_id' => $order->id,
                ]);

                return $this->redirectToFrontend($validated, -1);
            }

            if ($resCode !== 0) {
                $order->update(['status' => $resCode]);
                $transaction->update(['status' => $resCode]);

                return $this->redirectToFrontend($validated, $resCode);
            }

            $expectedAmount = (int) ($order->amount * Variable::getRate($order->asset));

            try {
                $response = sadad()
                    ->token($validated['Token'])
                    ->verification()
                    ->send();
            } catch (\RuntimeException $exception) {
                Log::error('Sadad verify call failed.', [
                    'order_id' => $order->id,
                ]);

                return $this->redirectToFrontend($validated, -1);
            }

            if (! $response->success()) {
                $order->update(['status' => $response->status()]);
                $transaction->update(['status' => $response->status()]);

                return $this->redirectToFrontend($validated, $response->status());
            }

            if ($response->orderId() !== null && $response->orderId() !== (int) $order->id) {
                Log::critical('Sadad verify order ID mismatch.', [
                    'order_id' => $order->id,
                    'verified_order_id' => $response->orderId(),
                ]);

                return $this->redirectToFrontend($validated, -1);
            }

            if ($response->amount() !== null && $response->amount() !== $expectedAmount) {
                Log::critical('Sadad verify amount mismatch.', [
                    'order_id' => $order->id,
                    'expected_amount' => $expectedAmount,
                    'verified_amount' => $response->amount(),
                ]);

                return $this->redirectToFrontend($validated, -1);
            }

            $order->update(['status' => $response->status()]);

            $transaction->update([
                'status' => $response->status(),
                'ref_id' => $response->referenceId(),
            ]);

            $user = $order->user;

            if ($user->can('canGetBonus', $order)) {
                $user->firstOrder()->create([
                    'type' => $order->asset,
                    'amount' => $order->amount,
                    'date' => jdate(now())->format('Y/m/d'),
                    'bonus' => $order->amount * 0.5,
                ]);

                $bonus = $order->amount * 0.5;
                $user->wallet->increment($order->asset, $order->amount + $bonus);
            } else {
                $user->wallet->increment($order->asset, $order->amount);
            }

            $order->payment()->create([
                'user_id' => $user->id,
                'ref_id' => $response->referenceId(),
                'card_pan' => $validated['HashedCardNo'] ?? $validated['PrimaryAccNo'] ?? 'card-hash',
                'gateway' => 'sadad',
                'amount' => $expectedAmount,
                'product' => $order->asset,
            ]);

            if ($order->asset !== 'irr') {
                ReferralService::referral($user, $order);
            }

            $user->notify(new TransactionNotification($order));
            $user->deposit();

            return $this->redirectToFrontend($validated, $resCode);
        });
    }

    /**
     * Redirect to the frontend with an allowlisted set of query parameters.
     *
     * @param  array<string, mixed>  $validated
     */
    private function redirectToFrontend(array $validated, int $resCode): RedirectResponse
    {
        $params = [
            'OrderId' => $validated['OrderId'],
            'ResCode' => $resCode,
            'status' => $resCode,
        ];

        if (! empty($validated['HashedCardNo'])) {
            $params['HashedCardNo'] = $validated['HashedCardNo'];
        }

        return redirect()->away(
            config('sadad.frontend_redirect_url') . '?' . http_build_query($params)
        );
    }
}
