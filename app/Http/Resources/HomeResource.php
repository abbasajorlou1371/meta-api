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
            $this->mergeWhen(isset($this->user), [
                'user' => new UserResource($this->user)
            ]),
            $this->mergeWhen(isset($this->top_players), [
                'top_players' => $this->top_players,
            ]),
            'features' => $this->featureRepository->getHomePageFeatures(),
            $this->mergeWhen($this->user && $this->user->features->count() > 0, [
                'feature_hourly_profit_info' => hourlyProfitInfo($this->user),
            ])
        ];
    }
}
