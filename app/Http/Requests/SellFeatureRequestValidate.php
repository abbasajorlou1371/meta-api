<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SellFeatureRequestValidate extends FormRequest
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
            'price_psc' => 'nullable|numeric|min:0',
            'price_irr' => 'nullable|numeric|min:0',
            'minimum_price_percentage' => 'nullable|integer|min:80',
        ];
    }
}
