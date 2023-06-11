<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->verified();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'bank_name' => 'required|string|min:2',
            'shaba_num' => 'required|ir_sheba|unique:bank_accounts,shaba_num',
            'card_num'  => 'required|ir_bank_card_number|unique:bank_accounts,card_num'
        ];
    }
}
