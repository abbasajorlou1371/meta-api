<?php

namespace App\Http\Requests;

use App\Rules\SecureFileUpload;
use Illuminate\Foundation\Http\FormRequest;

class TicketResponseRequest extends FormRequest
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
            'response' => 'required|string|max:500',
            'attachment' => ['nullable', 'file', new SecureFileUpload(['png', 'jpg', 'pdf'], 5000)]
        ];
    }
}
