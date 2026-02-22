<?php

namespace App\Http\Requests;

use App\Rules\SecureFileUpload;
use Illuminate\Foundation\Http\FormRequest;

class NoteRequest extends FormRequest
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
            'title' => 'required|string|max:130',
            'content' => 'required|string|max:2000',
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['required', 'file', new SecureFileUpload(['png', 'jpg', 'jpeg', 'pdf'], 5000)],
        ];
    }
}
