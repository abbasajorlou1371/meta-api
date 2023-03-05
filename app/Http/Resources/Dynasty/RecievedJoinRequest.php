<?php

namespace App\Http\Resources\Dynasty;

use App\Constants\FamilyMembersType;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Constants\JoinRequestStatus;

class RecievedJoinRequest extends JsonResource
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
            'status' => $this->status,
            'relationship' => $this->getRelationShipTitle(),
            $this->mergeWhen(request()->routeIs('joinRequests.recieved.show'), [
                'message' => $this->message,
                $this->mergeWhen($this->relationship === 'offspring', [
                    'permissions' => [
                        'BFR' => $this->toUser->permissions?->BFR,
                        'SF' => $this->toUser->permissions?->SF,
                        'W' => $this->toUser->permissions?->W,
                        'JU' => $this->toUser->permissions?->JU,
                        'DM' => $this->toUser->permissions?->DM,
                        'PIUP' => $this->toUser->permissions?->PIUP,
                        'PITC' => $this->toUser->permissions?->PITC,
                        'PIC' => $this->toUser->permissions?->PIC,
                        'ESOO' => $this->toUser->permissions?->ESOO,
                        'COTB' => $this->toUser->permissions?->COTB,
                    ]
                ])
            ])
        ];
    }
}
