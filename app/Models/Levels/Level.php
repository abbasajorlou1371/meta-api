<?php

namespace App\Models\Levels;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Level extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'score' => 'integer',
    ];

    /**
     * Get the users associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Get the prize associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function prize()
    {
        return $this->hasOne(LevelPrize::class);
    }

    /**
     * Get the image associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    /**
     * Get the score percentage required to reach the next level for a given user.
     *
     * @param  User  $user  The user for which to calculate the score percentage.
     * @return int  The score percentage to reach the next level.
     */
    public function getScorePercentageToNextLevel(User $user): int
    {
        $nextLevel = Level::find($this->id + 1);
        return $nextLevel
            ? (int)($user->score / $nextLevel->score * 100)
            : 0;
    }

    /**
     * Get the general information associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function generalInfo(): HasOne
    {
        return $this->hasOne(LevelGeneralInfo::class);
    }

    /**
     * Get the gem associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gem(): HasOne
    {
        return $this->hasOne(LevelGem::class);
    }

    /**
     * Get the licenses associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function licenses(): HasOne
    {
        return $this->hasOne(LevelLicense::class);
    }

    /**
     * Get the gift associated with the level.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function gift(): HasOne
    {
        return $this->hasOne(LevelGift::class);
    }
}
