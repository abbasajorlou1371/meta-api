<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BuyFeatureRequestValidate extends FormRequest
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
            'note' => 'nullable',
            'price_psc' => 'nullable|numeric|min:0|max:100000000',
            'price_irr' => 'nullable|numeric|min:0|max:10000000000',
        ];
    }

    public function messages()
    {
        return [
            'price_psc.required' => 'قیمت را وارد کنید',
            'price_psc.min' => 'کمترین مقدار 1 میباشد',
            'price_psc.max' => 'بیشترین مقدار 10000000000 می باشد',
            'price_irr.min' => 'کمترین مقدار 1 میباشد',
            'price_irr.max' => 'بیشترین مقدار 10000000000 می باشد',
        ];
    }
}
