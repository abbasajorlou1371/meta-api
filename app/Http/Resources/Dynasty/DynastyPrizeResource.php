<?php

namespace App\Http\Resources\Dynasty;

use Illuminate\Http\Resources\Json\JsonResource;

class DynastyPrizeResource extends JsonResource
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
            'psc' => $this->prize->psc,
            'satisfaction' => number_format($this->prize->satisfaction * 100),
            'introducation_profit_increase' => number_format($this->prize->introduction_profit_increase * 100),
            'accumulated_capital_reserve' => number_format($this->prize->accumulated_capital_reserve * 100),
            'data_storage' => number_format($this->prize->data_storage * 100),
            'message' => $this->message,
        ];
    }
}
