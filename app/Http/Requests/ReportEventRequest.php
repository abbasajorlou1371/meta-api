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
            'suspecious_citizen' => 'nullable|string|exists:users,code',
            'event_description' => 'required|string|max:500'
        ];
    }
}
