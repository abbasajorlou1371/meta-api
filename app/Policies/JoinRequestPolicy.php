<?php

namespace App\Policies;

use App\Models\Dynasty\JoinRequest;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class JoinRequestPolicy
{
    use HandlesAuthorization;

    public function view(User $user, JoinRequest $joinRequest)
    {
        return $joinRequest->fromUser->is($user) || $joinRequest->toUser->is($user);
    }

    public function delete(User $user, JoinRequest $joinRequest)
    {
        return $joinRequest->fromUser->is($user) && $joinRequest->status === 0;
    }

    public function accept(User $user, JoinRequest $joinRequest)
    {
        return $joinRequest->toUser->is($user) && $joinRequest->status === 0 ;
    }

    public function reject(User $user, JoinRequest $joinRequest)
    {
        return $joinRequest->toUser->is($user) && $joinRequest->status === 0 ;
    }
}
