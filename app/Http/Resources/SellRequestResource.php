<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SellRequestResource extends JsonResource
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
            $this->mergeWhen(! empty($this->message), [
                'message' => $this->message,
            ]),
            'id' => $this->id,
            'price_psc' => $this->price_psc,
            'price_irr' => $this->price_irr,
            'status' => $this->status,
            'feature' => new FeatureResource($this->feature),
        ];
    }
}
