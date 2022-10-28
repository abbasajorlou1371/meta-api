<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class FollowPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can follow another user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function follow(User $user, User $user_to_follow)
    {
        return $user->id === $user_to_follow->id
        || $user->following()->firstWhere('following_id', $user_to_follow->id)
        ? abort(401,'عملیات با خطا مواجه شد')
        : true;
    }
}
