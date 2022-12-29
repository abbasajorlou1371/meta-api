<?php

namespace App\Http\Resources\Dynasty;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;
use App\Http\Resources\Dynasty\FamilyMemberResource;
use App\Http\Resources\FeatureResource;

class DynastyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'user-has-dynasty' => true,
            'id' => $this->id,
            'family_id' => $this->family->id,
            'created_at' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'profile-image' => $this->user->profilePhotos->first()?->url,
            'dynasty-feature' => [
                'id' => $this->feature->id,
                'properties_id' => $this->feature->properties->id,
                'area' => $this->feature->properties->area,
                'density' => $this->feature->properties->density,
                'feature-profit-increase' => $this->feature->properties->stability > 1000 ?
                number_format($this->feature->properties->stability / 1000 - 1, 3)
                : 0,
                'family-members-count' => $this->family->familyMembers->count(),
                'last-updated' => Jalalian::forge($this->updated_at)->format('Y/m/d H:m:s')
            ],
            'features' => $request->user()->features
                ->reject(function ($feature) {
                    return $feature->properties->karbari !== 'm' || $feature->id == $this->feature->id;
                })
                ->map(function ($feature) {
                    return [
                        'id' => $feature->id,
                        'properties_id' => $feature->properties->id,
                        'stability' => $feature->properties->stability
                    ];
                })
        ];
    }
}
