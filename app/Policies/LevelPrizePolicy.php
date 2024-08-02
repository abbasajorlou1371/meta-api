<?php

namespace App\Policies;

use App\Models\Levels\LevelPrize;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LevelPrizePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can recieve the prize.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Levels\LevelPrize  $levelPrize
     * @return mixed
     */
    public function recievePrize(User $user, LevelPrize $levelPrize)
    {
        return $user->recievedLevelPrizes()->where('level_prize_id', $levelPrize->id)->doesntExist();
    }
}
