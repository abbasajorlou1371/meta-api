<?php

namespace App\Http\Resources\Dynasty;

use App\Constants\FamilyMembersType;
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
            "member" => FamilyMembersType::familyMembersTypeList()[$this->member],
            "satisfaction" => $this->satisfaction,
            "introduction_profit_increase" => $this->introduction_profit_increase * 100,
            "accumulated_capital_reserve" => $this->accumulated_capital_reserve * 100,
            "data_storage" => $this->data_storage * 100,
            "psc" => $this->psc,
        ];
    }
}
