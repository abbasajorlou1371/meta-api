<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingNotificationsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('update', $this->setting);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'announcements_sms' => 'required|boolean',
            'announcements_email' => 'required|boolean',
            'reports_sms' => 'required|boolean',
            'reports_email' => 'required|boolean',
            'login_verification_sms' => 'required|boolean',
            'login_verification_email' => 'required|boolean',
            'transactions_sms' => 'required|boolean',
            'transactions_email' => 'required|boolean',
            'trades_sms' => 'required|boolean',
            'trades_email' => 'required|boolean',
        ];
    }
}
