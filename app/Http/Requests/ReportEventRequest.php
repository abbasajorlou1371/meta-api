<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReportEventRequest extends FormRequest
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
            'suspecious_citizen' => ['nullable',
                function($attribute, $value, $fail) {
                    if(! preg_match('/hm-/i', $value))
                    {
                        $fail('شناسه کاربری وارد شده صحیح نیست');
                    } else if($value == request()->user()->code)
                    {
                        $fail('شما نمی توانید خود را به عنوان مضنون معرفی کنید');
                    }
                }
            ],
            'event_description' => 'required|max:300'
        ];
    }

    public function messages()
    {
        return [
            'event_description.required' => 'شرح واقعه را وارد کنید',
            'event_description.max' => 'تعداد حداکثر مجاز 300 کاراکتر می باشد'
        ];
    }
}
