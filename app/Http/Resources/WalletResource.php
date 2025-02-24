<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WalletResource extends JsonResource
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
            'psc' => formatCompactNumber($this->psc),
            'irr' => formatCompactNumber($this->irr),
            'red' => formatCompactNumber($this->red),
            'blue' => formatCompactNumber($this->blue),
            'yellow' => formatCompactNumber($this->yellow),
            'satisfaction' => number_format($this->satisfaction, 1),
            'effect' => $this->effect,
        ];
    }
}
