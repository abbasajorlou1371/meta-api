<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingsResource extends JsonResource
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
            'announcements_sms'           => $this->announcements_sms,
            'announcements_email'           => $this->announcements_email,
            'reports_sms'                 => $this->reports_sms,
            'reports_email'                 => $this->reports_email,
            'login_verification_sms'    => $this->login_verification_sms,
            'login_verification_email'    => $this->login_verification_email,
            'transactions_sms'   => $this->transactions_sms,
            'transactions_email'   => $this->transactions_email,
            'trades_sms'         => $this->trades_sms,
            'trades_email'         => $this->trades_email,
        ];
    }
}
