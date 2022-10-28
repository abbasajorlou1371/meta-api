<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ];
    }

    public function  messages()
    {
        return [
            'name' => [
                'required' => 'نام خود را وارد کنید',
                'string' => 'نام صحیح نمی باشد'
            ],
            'email' => [
                'required' => 'ایمیل را وارد کنید',
                'email' => 'آدرس ایمیل صحیح نیست',
                'unique' => 'آدرس ایمیل قبلا استفاده شده است'
            ],
            'password' => [
                'required' => 'رمز عبور را وارد کنید',
                'min' => 'رمز عبور باید حداقل 8 کاراکتر باشد'
            ]
        ];
    }
}
