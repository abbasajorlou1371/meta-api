<?php

namespace App\Http\Resources\Dynasty;

use App\Constants\FamilyMembersType;
use App\Models\Variable;
use Illuminate\Http\Resources\Json\JsonResource;

class IntroductionPrizeResource extends JsonResource
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
            "member" => $this->getMemberTitle(),
            "satisfaction" => $this->satisfaction,
            "introduction_profit_increase" => (int)($this->introduction_profit_increase * 100),
            "accumulated_capital_reserve" => (int)($this->accumulated_capital_reserve * 100),
            "data_storage" => (int)($this->data_storage * 100),
            "psc" => number_format($this->psc / Variable::getRate('psc'), 2),
        ];
    }
}
