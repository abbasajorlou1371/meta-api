<?php

namespace App\Http\Resources\Dynasty;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;
use App\Http\Resources\Dynasty\FamilyMemberResource;
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
            'id' => $this->id,
            'created_at' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'family_members' => FamilyMemberResource::collection($this->family->familyMembers),
        ];
    }
}
