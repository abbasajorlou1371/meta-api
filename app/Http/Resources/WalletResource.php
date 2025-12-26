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
            'psc' => $this->psc,
            'irr' => $this->irr,
            'red' => $this->red,
            'blue' => $this->blue,
            'yellow' => $this->yellow,
            'satisfaction' => $this->satisfaction,
            'effect' => $this->effect,
        ];
    }
}
