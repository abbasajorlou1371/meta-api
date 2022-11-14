<?php

namespace App\Http\Resources\PublicProfile;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

class PersonalInfo extends JsonResource
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
            $this->mergeWhen($this->profilePhoto, [
                'profilePhoto' => $this->profilePhoto?->url,
            ]),
            'kyc' => [
                $this->mergeWhen($this->verified(), [
                    'fname' => $this->kyc?->fname,
                    'lname' => $this->kyc?->lname,
                    'birth_date' => Jalalian::forge($this->kyc?->birthdate)->format('Y/m/d'),
                    'phone' => $this?->phone,
                    'email' => $this?->email,
                    'address' => $this->kyc?->address,
                ]),
            ],
            'about' => '',
            'code' => $this->code,
            'name' => $this->name,
            'score' => $this->score,
            'score_percentage_to_next_level' => getScorePercentageToNextLevel($this->level, $this->score),
            'registered_at' => Jalalian::forge($this->email_verified_at)->format('Y/m/d'),
            $this->mergeWhen($this->customs, [
                'customs' => [
                    'occupation' => $this->customs?->occupation,
                    'education' => $this->customs?->education,
                    'loved_city' => $this->customs?->loved_city,
                    'loved_country' => $this->customs?->loved_country,
                    'loved_language' => $this->customs?->loved_language,
                    'prediction' => $this->customs?->prediction,
                    $this->mergeWhen($this->customs?->passions, [
                        'passions' => [
                            "music" =>  $this->customs?->passions->music,
                            "sport_health" =>  $this->customs?->passions->sport_health,
                            "art" =>  $this->customs?->passions->art,
                            "language_culture" =>  $this->customs?->passions->language_culture,
                            "philosophy" =>  $this->customs?->passions->philosophy,
                            "animals_nature" =>  $this->customs?->passions->animals_nature,
                            "aliens" =>  $this->customs?->passions->aliens,
                            "food_cooking" =>  $this->customs?->passions->food_cooking,
                            "travel_leature" =>  $this->customs?->passions->travel_leature,
                            "manufacturing" =>  $this->customs?->passions->manufacturing,
                            "science_technology" =>  $this->customs?->passions->science_technology,
                            "space_time"  =>  $this->customs?->passions->space_time,
                            "history" =>  $this->customs?->passions->history,
                            "politics_economy" =>  $this->customs?->passions->politics_economy,
                        ]
                    ]),
                ]
            ]),
            $this->mergeWhen($this->level, [
                'level' => [
                    'name' => $this->level?->name,
                    'image' => $this->level?->image?->url,
                ]
            ]),
            'avatar' => 'https://irpsc.com/gb.glb',
        ];
    }
}
