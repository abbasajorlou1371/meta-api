<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class TicketResponseResource extends JsonResource
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
            'ticket_id' => (string)$this->ticket->id,
            'response' => $this->response,
            $this->mergeWhen($this->attachment, [
                'attachment' => $this->attachment,
            ]),
            'responser_name' => $this->ticket->responser_name,
            'date' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'time' => Jalalian::forge($this->created_at)->format('H:m:s'),
        ];
    }
}
