<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Reset;
use Illuminate\Validation\Rule;

class ResetInfoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Reset::resetInfo(
            $this->user(),
            $this->has('email') ? 'email' : 'phone'
        )
            ->count() < 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'phone' =>
            Rule::when($this->routeIs('reset.phone'), [
                'required',
                'ir_mobile',
                'unique:users,phone'
            ]),
            'email' =>
            Rule::when($this->routeIs('reset.email'), [
                'required',
                'email',
                'unique:users,email'
            ]),
        ];
    }
}
