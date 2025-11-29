<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserFeatureResource extends JsonResource
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
            'properties' => new FeaturePropertiesResource($this->whenLoaded('properties')),
            'images' => FeatureImageResource::collection($this->whenLoaded('images')),
            'seller' => $this->whenLoaded('latestTraded', function () {
                return [
                    'id' => $this->latestTraded?->seller?->id,
                    'name' => $this->latestTraded?->seller?->name,
                    'code' => $this->latestTraded?->seller?->code,
                ];
            }),

            'geometry' => $this->whenLoaded('geometry', function () {
                return $this->geometry?->coordinates;
            }),

        ];
    }
}
