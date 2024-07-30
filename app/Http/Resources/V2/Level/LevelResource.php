<?php

namespace App\Http\Resources\V2\Level;

use Illuminate\Http\Resources\Json\JsonResource;

class LevelResource extends JsonResource
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
            'image' => $this->whenLoaded('image', config('app.admin_panel_url') . '/uploads/' . $this->image->url),
            'background_image' => $this->whenNotNull($this->background_image),
            'general_info' => $this->whenLoaded('generalInfo', function () {
                return [
                    'score' => $this->generalInfo->score,
                    'rank' => $this->generalInfo->rank,
                    'png_file' => $this->generalInfo->png_file,
                    'fbx_file' => $this->generalInfo->fbx_file,
                    'gif_file' => $this->generalInfo->gif_file,
                    'description' => $this->generalInfo->description,
                ];
            })
        ];
    }
}
