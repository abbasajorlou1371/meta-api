<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\VideoTutorialResource;

class VideoSubCategoryResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image_url,
            'icon' => $this->icon_url,
            'likes_count' => $this->whenCounted('likes'),
            'dislikes_count' => $this->whenCounted('dislikes'),
            'views_count' => $this->whenCounted('views'),
            'videos_count' => $this->whenCounted('videos'),
            'description' => $this->when($request->routeIs('tutorials.subcategories.show'), $this->description),
            'videos' => VideoTutorialResource::collection($this->whenLoaded('videos'))
        ];
    }
}
