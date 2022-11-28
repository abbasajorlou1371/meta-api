<?php

namespace App\Http\Resources\Dynasty;

use Illuminate\Http\Resources\Json\JsonResource;

class FamilyMembersResource extends JsonResource
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
            'id' => $this->user->id,
            'code' => $this->user->code,
            'profile-photos' => $this->user->profilePhotos,
            'relationship' => $this->relationship,
            'level' => $this->user->level,
        ];
    }
}
