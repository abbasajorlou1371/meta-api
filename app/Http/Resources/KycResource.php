<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KycResource extends JsonResource
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
            $this->mergeWhen(isset($this->message), [
            'message' => $this->message ?? '',
            ]),
            'id' => (string)$this->id,
            'user_id' => $this->user_id,
            'shaba' => $this->shaba,
            'bank' => $this->bank,
            'melli_card' => $this->melli_card,
            'prove_picture' => $this->prove_picture,
            'resume' => $this->resume,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'father_name' => $this->father_name,
            'melli_code' => $this->melli_code,
            'birthdate' => $this->birthdate,
            'province' => $this->province,
            'city' => $this->city,
            'number' => $this->number,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'site' => $this->site,
        ];
    }
}
