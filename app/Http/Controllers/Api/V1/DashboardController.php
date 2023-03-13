<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AssetResource;
use App\Http\Resources\LatestTransactionResource;
use App\Http\Resources\ProfileResource;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        return new ProfileResource($request->user());
    }

    public function latestTransaction(Request $request)
    {
        $user = User::whereId($request->user()->id)
            ->with(['latestPayment', 'latestTransaction', 'latestOrder'])
            ->first();
        return new LatestTransactionResource($user);
    }

    public function transactions(Request $request)
    {
        return TransactionResource::collection(
            Transaction::whereBelongsTo($request->user())->simplePaginate(10)
        );
    }

    public function showWallet(Request $request)
    {
        return new AssetResource($request->user()->assets);
    }
}
