<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class profileLimitationResource extends JsonResource
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
            'limiter_user_id' => $this->limiter_user_id,
            'limited_user_id' => $this->limited_user_id,
            'options' => $this->options,
            'note' => $this->when(auth()->id() == $this->limiter_user_id, $this->note),
        ];
    }
}
