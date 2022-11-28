<?php

namespace App\Policies;

use App\Models\Dynasty\JoinRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JoinRequestPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function accept(User $user, JoinRequest $joinRequest)
    {
        return $user->id == $joinRequest->to_user && $joinRequest->status == 1 ? true : false;
    }

    public function reject(User $user, JoinRequest $joinRequest)
    {
        return $user->id == $joinRequest->to_user && $joinRequest->status == 1 ? true : false;
    }
}
