<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Passion extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_id',
        'music',
        'sport_health',
        'art',
        'language_culture',
        'philosophy',
        'animals_nature',
        'aliens',
        'food_cooking',
        'travel_leature',
        'manufacturing',
        'science_technology',
        'space_time',
        'history',
        'politics_economy'
    ];

    public function custom() {
        return $this->belongsTo(Custom::class);
    }
}
