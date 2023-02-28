<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class UserEventReportResource extends JsonResource
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
            'suspecious_citizen' => $this->suspecious_citizen,
            'event_description' => $this->event_description,
            'status' => $this->status,
            'closed' => $this->closed,
            'date' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'time' => Jalalian::forge($this->created_at)->format('H:m:s'),
            'responses' => UserEventReportResponseResource::collection($this->responses)
        ];
    }
}
