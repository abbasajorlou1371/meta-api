<?php

namespace App\Http\Resources;

use App\Constants\TicketStatus;
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
            $this->mergeWhen(isset($this->message), [
                'message' => $this->message,
            ]),
            'id' => (string)$this->id,
            'title' => $this->title,
            'content' => $this->content,
            'attachment' => $this->attachment,
            'sender' => $this->sender->name ?? 'سیستم',
            'responser_name' => $this->responser_name,
            $this->mergeWhen($this->reciever, [
                'reciever' => $this->reciever->name ?? "",
            ]),
            $this->mergeWhen($this->department, [
                'reciever' => ticketDepartmentsTitle($this->department)
            ]),
            'code' => $this->code,
            $this->mergeWhen(isset($this->responses), [
                'responses' => TicketResponseResource::collection($this->responses),
            ]),
            'status' => ticketStatusTitle($this->status),
            'created_at' => \Morilog\Jalali\Jalalian::forge($this->created_at)->format('Y/m/d'),
        ];
    }
}
