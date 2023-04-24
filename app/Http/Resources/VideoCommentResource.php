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
            'commenter_name' => $this->user->name,
            'commenter_code' => $this->user->code,
            'commenter_image' => $this->user->profilePhotos->last()?->url,
            'content' => $this->content,
            'created_at' => jdate($this->created_at)->format('Y/m/d'),
            'likes' => $this->interactions->where('liked', 1)->count(),
            'dislikes' => $this->interactions->where('liked', 0)->count()
        ];
    }
}
