<?php

namespace App\Http\Controllers\Api\V1\Dynasty;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dynasty\DynastyPrizeResource;
use App\Models\Dynasty\RecievedPrize;
use App\Models\User;
use Illuminate\Http\Request;

class DynastyPrizeController extends Controller
{
    public function index()
    {
        $user = request()->user();
        $prizes = $user->recievedDynastyPrizes;
        if(!count($prizes))
        {
            return response()->json(['error' => 'جوایزی دریافت نشده است!'], 404);
        }

        return DynastyPrizeResource::collection($prizes);
    }

    public function show(RecievedPrize $recievedDynastyPrize)
    {
        return new DynastyPrizeResource($recievedDynastyPrize);
    }

    public function getPrize(User $user, RecievedPrize $recievedDynastyPrize)
    {
        $prize = $recievedDynastyPrize->prize;
        $user->assets->increment('psc', $prize->psc / currentPscPrice());
        $user->assets->increment('satisfaction', $prize->satisfaction);
        $variables = $user->variables;
        $variables->update([
            'referral_profit' => $variables->referral_profit + ($variables->referral_profit * $prize->introduction_profit_increase),
            'data_storage' => $variables->data_storage + ($variables->data_storage * $prize->data_storage),
            'withdraw_profit' => $variables->withdraw_profit + ($variables->withdraw_profit * $prize->accumulated_capital_reserve),
        ]);
        $recievedDynastyPrize->delete();
        return response()->json(['message' => 'جوایز دریافت شد!'], 200);
    }
}
