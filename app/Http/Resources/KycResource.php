<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

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
            'id' => (string)$this->id,
            'melli_card' => config('rgb.ftp-endpoint').$this->melli_card,
            'prove_picture' => config('rgb.ftp-endpoint').$this->prove_picture,
            'resume' => config('rgb.ftp-endpoint').$this->resume,
            'fname' => $this->fname,
            'lname' => $this->lname,
            'father_name' => $this->father_name,
            'melli_code' => $this->melli_code,
            'birthdate' => Jalalian::forge($this->birthdate)->format('Y/m/d'),
            'province' => $this->province,
            'city' => $this->city,
            'number' => $this->number,
            'postal_code' => $this->postal_code,
            'address' => $this->address,
            'site' => $this->site,
            'status' => $this->status,
            $this->mergeWhen($this->errors->count() > 0, [
                'errors' => KycErrorsResource::collection($this->errors),
            ])
        ];
    }
}
