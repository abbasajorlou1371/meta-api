<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class UserBuildingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $attributes = collect($this->buildingModel?->attributes ?? []);

        return [
            'feature_properties_id' => Str::upper($this->feature->properties->id),
            'karbari' => $this->feature->properties->karbari,
            'area' => $this->getAttributeValue($attributes, 'area'),
            'visitors' => $this->getAttributeValue($attributes, 'visitors'),
            'empty_units' => $this->getAttributeValue($attributes, 'empty_units'),
            'floors' => $this->getAttributeValue($attributes, 'floors'),
            'construction_end_date' => $this->construction_end_date
                ? jdate($this->construction_end_date)->format('Y/m/d')
                : null,
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, array<string, mixed>>  $attributes
     */
    private function getAttributeValue($attributes, string $slug): mixed
    {
        $attribute = $attributes->firstWhere('slug', $slug);

        return $attribute['value'] ?? null;
    }
}
