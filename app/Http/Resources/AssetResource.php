<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
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
            'user_id' => $this->id,
            'psc' => number_format($this->psc, 3, '.', ','),
            'irr' => number_format($this->irr, 3, '.', ','),
            'red' => number_format($this->red, 3, '.', ','),
            'blue' => number_format($this->blue, 3, '.', ','),
            'yellow' => number_format($this->yellow, 3, '.', ','),
            'satisfaction' => number_format($this->satisfaction, 1),
            'effect' => $this->effect,
        ];
    }
}
