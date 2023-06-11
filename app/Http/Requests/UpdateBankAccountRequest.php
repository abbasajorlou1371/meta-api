<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'bank_name' => 'required|min:2',
            'shaba_num' => 'required|ir_sheba|unique:bank_accounts,shaba_num',
            'card_num'  => 'required|ir_bank_card_number|unique:bank_accounts,card_num'
        ];
    }
}
