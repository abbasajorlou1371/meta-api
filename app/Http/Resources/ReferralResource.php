<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ReferralResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->whenLoaded('kyc', function () {
                return $this->kyc->full_name;
            }) ?? $this->name,
            'image' => $this->whenLoaded('latestProfilePhoto', function () {
                return $this->latestProfilePhoto->url;
            }),
            'referrerOrders' => $this->whenLoaded('referrerOrders', function () {
                return $this->referrerOrders->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'amount' => $order->amount,
                        'created_at' => jdate($order->created_at)->format('Y-m-d H:i:s'),
                    ];
                });
            }),
        ];
    }
}
