<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\TicketResponseResource;

class TicketResource extends JsonResource
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
            'title' => $this->title,
            'sender' => $this->whenLoaded('sender', function () {
                return [
                    'name' => $this->sender->name,
                    'code' => $this->sender->code,
                    'profile-photo' => $this->sender->latestProfilePhoto?->url,
                ];
            }),
            'reciever' => $this->whenLoaded('reciever', function () {
                return [
                    'name' => $this->reciever->name,
                    'code' => $this->sender->code,
                    'profile-photo' => $this->reciever->latestProfilePhoto?->url,
                ];
            }),
            'department' => $this->whenNotNull($this->department),
            'code' => $this->code,
            'attachment' => $this->attachment,
            'content' => $this->content,
            'status' => $this->status,
            'date' => jdate($this->updated_at)->format('Y/m/d'),
            'time' => jdate($this->updated_at)->format('H:m:s'),
            'responses' => TicketResponseResource::collection($this->whenLoaded('responses')),
        ];
    }
}
