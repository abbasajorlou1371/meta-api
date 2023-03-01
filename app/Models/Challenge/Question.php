<?php

namespace App\Models\Challenge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function correctAnswer(): HasOne
    {
        return $this->hasOne(Answer::class)->ofMany([],function(Builder $query) {
            $query->where('is_correct', true);
        });
    }

    public function image(): Attribute
    {
        return Attribute::make(
            get: fn($value) => config('rgb.ftp-endpoint').'public/challenge/'.$value.'.png'
        );
    }
}
