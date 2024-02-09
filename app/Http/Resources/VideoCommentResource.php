<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class VideoCommentResource extends JsonResource
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
            'video_id' => $this->commentable->id,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'code' => $this->user->code,
                'image' => $this->user->profilePhotos->first()?->url
            ],
            'content' => $this->content,
            'likes' => $this->likes_count,
            'dislikes' => $this->dislikes_count,
            'created_at' => jdate($this->created_at)->format('Y/m/d')
        ];
    }
}
