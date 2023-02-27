<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class ReportResource extends JsonResource
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
            'id' => (string)$this->id,
            'title' => $this->title,
            'url' => $this->url,
            'subject' => $this->subject,
            $this->mergeWhen(request()->routeIs('reports.show'), [
                'content' => $this->content,
                'attachment' => $this->image?->url,
            ]),
            'date' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'time' => Jalalian::forge($this->created_at)->format('H:m:s'),
        ];
    }
}
