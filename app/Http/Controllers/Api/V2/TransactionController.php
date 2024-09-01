<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index()
    {
        $transactions = Transaction::whereBelongsTo(auth()->user())
            ->latest()->simplePaginate();

        return TransactionResource::collection($transactions);
    }

    public function search(Request $request)
    {
        $request->validate([
            'ref_id' => 'required|integer'
        ]);

        $transactions = Transaction::whereBelongsTo(auth()->user())
            ->where('ref_id', $request->ref_id)
            ->latest()->simplePaginate();

        return TransactionResource::collection($transactions);
    }
}
