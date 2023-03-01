<?php

namespace App\Http\Controllers\Api\V1\Dynasty;

use App\Http\Controllers\Controller;
use App\Http\Resources\Dynasty\DynastyPrizeResource;
use App\Models\Dynasty\RecievedPrize;
use App\Models\Variable;
use Illuminate\Http\Request;

class DynastyPrizeController extends Controller
{
    public function index()
    {
        return DynastyPrizeResource::collection(request()->user()->recievedDynastyPrizes);
    }

    public function show(RecievedPrize $recievedPrize)
    {
        return new DynastyPrizeResource($recievedPrize);
    }

    public function store(Request $request, RecievedPrize $recievedPrize)
    {
        $user = $request->user();
        $prize = $recievedPrize->prize;

        $user->assets->increment('psc', $prize->psc / Variable::getRate('psc'));
        $user->assets->increment('satisfaction', $prize->satisfaction);

        $variables = $user->variables;

        $variables->update([
            'referral_profit' => $variables->referral_profit + ($variables->referral_profit * $prize->introduction_profit_increase),
            'data_storage' => $variables->data_storage + ($variables->data_storage * $prize->data_storage),
            'withdraw_profit' => $variables->withdraw_profit + ($variables->withdraw_profit * $prize->accumulated_capital_reserve),
        ]);

        $recievedPrize->delete();
        return response()->noContent();
    }
}
