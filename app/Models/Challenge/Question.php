<?php

namespace App\Models\Challenge;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    /**
     * @return HasMany
     */
    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }

    public function correctAnswer()
    {
        return $this->answers->where('is_correct', true)->first();
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => config('rgb.ftp-endpoint').'public/challenge/'.$value.'.png'
        );
    }

    public function scopeNotAnswered($query, $user) {
        return $query->where('');
    }
}
