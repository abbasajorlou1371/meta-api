<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Morilog\Jalali\Jalalian;

/**
 * @property mixed $updated_at
 * @property mixed $created_at
 * @property mixed $title
 * @property mixed $code
 * @property mixed $admin_id
 * @property mixed $id
 */
class QuestionResource extends JsonResource
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
            'code' => $this->code,
            'title' => $this->title,
            'image' => $this->image,
            'prize' => $this->prize,
            'participants' => $this->participants,
            'views' => $this->views,
            'created_at' => Jalalian::forge($this->created_at)->format('Y/m/d'),
            'answers' => AnswerResource::collection($this->answers),
        ];
    }
}
