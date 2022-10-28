<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
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
            'title' => 'required|string|max:250',
            'content' => 'required|string|max:500',
            'attachment' => 'nullable|file|mimes:png,jpg,bmp,pdf',
            'reciever_id' => 'nullable|integer',
            'department' => 'nullable|in:technical_support,citizens_safety,investment,inspection,protection,ztb',
        ];
    }

    public function messages() {
        return [
            'title.required' => 'عنوان تیکت را وارد کنید',
            'title.max' => 'تعداد کاراکترهای وارد شده بیش از حد مجاز می باشد',
            'content.required' => 'متن تیکت را وارد کنید',
            'attachment.mimes' => 'فرمت فایل انتخاب شده صحیح نیست',
            'department.in' => 'بخش انتخاب شده صحیح نیست'
        ];
    }

}
