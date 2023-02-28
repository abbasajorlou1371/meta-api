<?php

namespace App\Models\Level;

use App\Models\Image;
use App\Models\Level\Prize;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = [
        'created_at',
        'updated_at',
        'score',
        'laravel_through_key'
    ];

    public function prize()
    {
        return $this->hasOne(Prize::class);
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function getScorePercentageToNextLevel(User $user): int
    {
        $nextLevel = Level::find($this->id + 1);
        return $nextLevel
        ? (int)($user->score / $nextLevel->score * 100)
        : 0;
    }
}
