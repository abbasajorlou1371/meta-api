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
            'title' => $this->title,
            'image' => $this->image,
            'prize' => $this->prize,
            'participants' => $this->participants,
            'views' => $this->views,
            'creator_code' => $this->creator_code,
            'answers' => AnswerResource::collection($this->answers),
        ];
    }
}
