<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCustomRequest extends FormRequest
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
            'occupation' => 'nullable|string',
            'education' =>  'nullable|string',
            'memory' => 'nullable|string',
            'loved_city' => 'nullable|string',
            'loved_county' => 'nullable|string',
            'loved_languege' => 'nullable|string',
            'problem_solving' => 'nullable|string',
            'prediction' => 'nullable|string',
            'about' => 'required|string'
        ];
    }
}
