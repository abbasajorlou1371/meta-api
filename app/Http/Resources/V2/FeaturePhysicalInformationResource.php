<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeaturePhysicalInformationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'feature_id' => $this->feature_id,
            'group_name' => $this->group_name,
            'active_company' => $this->active_company,
            'physical_address' => $this->physical_address,
            'physical_postal_code' => $this->physical_postal_code,
            'postal_address' => $this->postal_address,
            'establishment_goal' => $this->establishment_goal,
            'activity' => new IsicCodeResource($this->whenLoaded('isicCode')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
