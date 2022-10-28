<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class HomeResource extends JsonResource
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
            $this->mergeWhen(! empty($this->user), [
                'user' => new UserResource($this->user)
            ]),
            $this->mergeWhen(! empty($this->top_players), [
                'top_players' => UserResource::collection($this->top_players)
            ]),
            // 'features' => $this->features,
            'packages' => new PackageResource($this->packages)
        ];
    }
}
