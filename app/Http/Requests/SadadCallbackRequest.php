<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SadadCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Sadad POSTs these fields after payment; ResCode must come from the gateway, not a client alias.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'OrderId' => ['required', 'integer', 'exists:orders,id'],
            'ResCode' => ['required', 'integer'],
            'Token' => ['required', 'string', 'max:255'],
            'HashedCardNo' => ['nullable', 'string', 'max:255'],
            'PrimaryAccNo' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $redirectUrl = config('sadad.frontend_redirect_url') . '?' . http_build_query([
            'ResCode' => -1,
            'status' => -1,
        ]);

        throw new HttpResponseException(redirect()->away($redirectUrl));
    }
}
