<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->whenLoaded('kyc', function () {
                return $this->kyc->fname . ' ' . $this->kyc->lname;
            }) ?? $this->name,
            'code' => $this->code,
            'score' => $this->score,
            'levels' => $this->whenLoaded('levels', function () {
                return [
                    'current' => $this->latest_level ? [
                        'id' => $this->latest_level->id,
                        'name' => $this->latest_level->name,
                        'slug' => $this->latest_level->slug,
                        'image' => config('app.admin_panel_url') . '/uploads/' . $this->latest_level->image->url,
                    ] : null,
                    'previous' => $this->levels->map(function ($level) {
                        return [
                            'id' => $level->id,
                            'name' => $level->name,
                            'slug' => $level->slug,
                            'image' => config('app.admin_panel_url') . '/uploads/' . $level->image->url,
                        ];
                    }),
                ];
            }),
            'profile_photo' => $this->latestProfilePhoto->url ?? null,
        ];
    }
}
