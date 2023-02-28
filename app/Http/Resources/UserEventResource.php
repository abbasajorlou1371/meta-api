<?php

namespace App\Http\Resources;

use App\Http\Resources\UserEventReportResource;
use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class UserEventResource extends JsonResource
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
            'event' => $this->event,
            'ip' => $this->ip,
            'device' => $this->device,
            'status' => $this->status ? 'موفق' : 'ناموفق',
            'date' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'time' => Jalalian::forge($this->created_at)->format('H:m:s'),
            $this->mergeWhen(request()->routeIs('user-events.show'), [
                'report' => new UserEventReportResource($this->report),
            ])
        ];
    }
}
