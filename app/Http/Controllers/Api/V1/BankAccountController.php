<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use App\Http\Requests\StoreBankAccountRequest;
use App\Http\Resources\BankAccountResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(BankAccount::class);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BankAccountResource::collection(request()->user()->bankAccounts);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreBankAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBankAccountRequest $request)
    {
        $bankAccount = $request->user()->bankAccounts()->create([
            'bank_name' => $request->bank_name,
            'shaba_num' => $request->shaba_num,
            'card_num' => $request->card_num,
            'status' => 0,
        ]);
        return new BankAccountResource($bankAccount);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function show(BankAccount $bankAccount)
    {
        return new BankAccountResource($bankAccount);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBankAccountRequest  $request
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'bank_name' => 'required|min:2',
            'shaba_num' => [
                'required',
                'ir_sheba',
                Rule::unique('bank_accounts')->ignore($bankAccount),
            ],
            'card_num'  => [
                'required',
                'ir_bank_card_number',
                Rule::unique('bank_accounts')->ignore($bankAccount),
            ]
        ]);

        $bankAccount->update([
            'bank_name' => $request->bank_name,
            'shaba_num' => $request->shaba_num,
            'card_num' => $request->card_num,
            'status' => 2,
        ]);

        // Delete the bank account's errors
        $bankAccount->errors()->delete();
        return new BankAccountResource($bankAccount);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BankAccount  $bankAccount
     * @return \Illuminate\Http\Response
     */
    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return response()->noContent();
    }
}
