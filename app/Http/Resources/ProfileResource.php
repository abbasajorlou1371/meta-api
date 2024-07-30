<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ProfileResource extends JsonResource
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
            'name' => $this->whenLoaded('kyc', function () {
                return $this->filterField('name', $this->kyc->full_name);
            }) ?? $this->filterField('name', $this->name),
            'code' => $this->filterField('code', $this->code),
            'registered_at' => $this->filterField('registered_at', jdate($this->email_verified_at)->format('Y/m/d')),
            'profile_images' => $this->whenLoaded('profilePhotos', function () {
                return $this->profilePhotos->map(function ($photo) {
                    return $photo->url;
                });
            }),
            'followers_count' => $this->filterField('followers_count', $this->followers_count),
            'following_count' => $this->filterField('following_count', $this->following_count),
        ];
    }

    /**
     * Filter a field based on privacy settings.
     *
     * @param string $field The name of the field to filter.
     * @param mixed $value The value of the field.
     * @return mixed|null The filtered value if the field is allowed, otherwise null.
     */
    private function filterField(string $field, mixed $value)
    {
        if (Auth::id() == $this->id) {
            return $value;
        }

        return isset($this->settings->privacy[$field])
            && $this->settings->privacy[$field] == 1 ? $value : null;
    }
}
