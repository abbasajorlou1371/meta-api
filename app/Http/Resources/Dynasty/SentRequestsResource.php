<?php

namespace App\Http\Resources\Dynasty;

use Illuminate\Http\Resources\Json\JsonResource;

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
            'to_user' => $this->whenLoaded('toUser', function () {
                return [
                    'id' => $this->toUser->id,
                    'code' => $this->toUser->code,
                    'name' => $this->toUser->name,
                ];
            }),
            'status' => $this->status,
            'relationship' => $this->getRelationShipTitle(),
            'date' => jdate($this->created_at)->format('Y/m/d'),
            'time' => jdate($this->created_at)->format('H:i'),
            'prize' => new DynastyPrizeResource($this->whenLoaded('requestPrize')),
            $this->mergeWhen(request()->routeIs('dynasty.requests.sent.show'), [
                'message' => $this->message,
            ]),
        ];
    }
}
