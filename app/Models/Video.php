<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Video extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected function fileName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('rgb.admin_panel_url') . $value
        );
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('rgb.admin_panel_url') . $value
        );
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function dislikes(): MorphMany
    {
        return $this->morphMany(Dislike::class, 'dislikeable');
    }
}
