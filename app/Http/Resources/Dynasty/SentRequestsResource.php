<?php

namespace App\Http\Resources\Dynasty;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\FamilyMembersType;

class SentRequestsResource extends JsonResource
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
            'from_user' => [
                'id' => $this->fromUser->id,
                'code' => $this->fromUser->code,
                'name' => $this->fromUser->name,
            ],
            'to_user' => [
                'id' => $this->toUser->id,
                'code' => $this->toUser->code,
                'name' => $this->toUser->name,
            ],
            'status' => $this->status,
            'relationship' => FamilyMembersType::familyMembersTypeList()[$this->relationship],
            'message' => $this->message,
        ];
    }
}
