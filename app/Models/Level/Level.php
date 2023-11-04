<?php

namespace App\Models\Level;

use App\Models\Image;
use App\Models\Level\Prize;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'score' => 'integer',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
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

    public function generalInfo(): HasOne
    {
        return $this->hasOne(LevelGeneralInfo::class);
    }

    public function gem(): HasOne
    {
        return $this->hasOne(LevelGem::class);
    }

    public function licenses(): HasOne
    {
        return $this->hasOne(LevelLicense::class);
    }

    public function gift(): HasOne
    {
        return $this->hasOne(LevelGift::class);
    }

    public function prizes(): HasOne
    {
        return $this->hasOne(LevelPrize::class);
    }
}
