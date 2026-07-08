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
use Dedoc\Scramble\Attributes\BodyParameter;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    #[Endpoint(
        title: 'Create order and get Sadad payment URL',
        description: 'Creates an order for purchasing virtual assets and returns a Bank Melli Sadad IPG payment link. Requires authentication and passes the `buyFromStore` policy gate.',
    )]
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

        Log::info('Sadad request', [
            'order_id' => $order->id,
            'amount' => $order->amount * $rate,
            'asset' => $order->asset,
            'callback_url' => sadad_callback_url(),
        ]);

        $response = sadad()
            ->orderId($order->id)
            ->amount($order->amount * $rate)
            ->asset($order->asset)
            ->request()
            ->callbackUrl(sadad_callback_url())
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

    #[Endpoint(
        title: 'Sadad payment callback',
        description: 'Public callback endpoint for Bank Melli Sadad IPG. Sadad POSTs form-encoded fields after payment; the server verifies the transaction and redirects the user to the frontend with `OrderId`, `ResCode`, and `status` query parameters.',
    )]
    #[BodyParameter('OrderId', description: 'Order primary key.', type: 'integer', required: true)]
    #[BodyParameter('ResCode', description: 'Sadad gateway result code (`0` indicates success).', type: 'integer', required: true)]
    #[BodyParameter('Token', description: 'Payment token — must match the token stored on the order transaction.', type: 'string', required: true)]
    #[BodyParameter('HashedCardNo', description: 'Hashed card number from Sadad.', type: 'string', required: false)]
    #[BodyParameter('PrimaryAccNo', description: 'Masked card number from Sadad.', type: 'string', required: false)]
    #[Response(
        status: 302,
        description: 'Redirects to `SADAD_FRONTEND_REDIRECT_URL` with `OrderId`, `ResCode`, `status`, and optionally `HashedCardNo`.',
        mediaType: 'text/html',
    )]
    #[Response(status: 404, description: 'Order not found.')]
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
