<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicFeatureListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'vod_id' => $this->properties->id,
            'address' => $this->properties->address,
            'area' => $this->properties->area,
            'density' => $this->properties->density,
            'karbari' => $this->properties->karbari,
            'owner_code' => $this->owner?->code,
            'price_psc' => $this->properties->price_psc,
            'price_irr' => $this->properties->price_irr,
            'center' => $this->computed_center,
            'label' => $this->properties->label,
            'images' => FeatureImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
