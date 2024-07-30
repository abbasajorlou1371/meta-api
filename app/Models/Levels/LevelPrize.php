<?php

namespace App\Models\Levels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LevelPrize extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Get the level that owns the LevelPrize
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * The users that belong to the LevelPrize
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'recieved_level_prizes');
    }
}
