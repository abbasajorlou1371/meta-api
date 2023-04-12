<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoTutorialResource extends JsonResource
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
            'description' => $this->description,
            'creator' => $this->creator_code,
            'video' => $this->fileName,
            'image' => $this->image,
            'visits' => $this->visits,
            'likes' => $this->likes->count(),
            'dislikes' => $this->dislikes->count(),
        ];
    }
}
