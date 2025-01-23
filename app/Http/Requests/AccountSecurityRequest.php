<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AccountSecurityRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $accountSecurity = $this->user()->accountSecurity;
        return $accountSecurity && $accountSecurity->user->is($this->user());
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'phone' => Rule::when(!$this->user()->hasVerifiedPhone(), [
                'required',
                'unique:users,phone',
                'ir_mobile',
            ]),
            'time' => 'required|integer|between:5,60',
        ];
    }
}
