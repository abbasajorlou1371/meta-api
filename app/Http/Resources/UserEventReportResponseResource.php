<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class UserEventReportResponseResource extends JsonResource
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
            'responser_name' => $this->responser_name,
            'response' => $this->response,
            'date' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'time' => Jalalian::forge($this->created_at)->format('H:m:s'),
        ];
    }
}
