<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationSettingsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'announcements_sms'           => $this->notifications['announcements_sms'],
            'announcements_email'           => $this->notifications['announcements_email'],
            'reports_sms'                 => $this->notifications['reports_sms'],
            'reports_email'                 => $this->notifications['reports_email'],
            'login_verification_sms'    => $this->notifications['login_verification_sms'],
            'login_verification_email'    => $this->notifications['login_verification_email'],
            'transactions_sms'   => $this->notifications['transactions_sms'],
            'transactions_email'   => $this->notifications['transactions_email'],
            'trades_sms'         => $this->notifications['trades_sms'],
            'trades_email'         => $this->notifications['trades_email'],
        ];
    }
}
