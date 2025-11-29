<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
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
            'content' => $this->content,
            'attachment' => $this->whenNotNull($this->attachment, function () {
                return url('uploads/' . $this->attachment);
            }),
            'date' => jdate($this->updated_at)->format('Y/m/d'),
            'time' => jdate($this->updated_at)->format('H:m:s'),
        ];
    }
}
