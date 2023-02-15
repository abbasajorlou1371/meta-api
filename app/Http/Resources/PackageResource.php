<?php

namespace App\Http\Resources;

use App\Models\Variable;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'asset' => $this->asset,
            'amount' => $this->amount,
            'unitPrice' => Variable::getRate($this->asset),
            'image' => $this->image?->url
        ];
    }
}
