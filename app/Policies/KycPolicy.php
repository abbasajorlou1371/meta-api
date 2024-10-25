<?php

namespace App\Policies;

use App\Models\Kyc;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class KycPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Kyc  $kyc
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Kyc $kyc)
    {
        return $kyc->user->is($user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Kyc  $kyc
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Kyc $kyc)
    {
        return $kyc->user->is($user) && $kyc->rejected();
    }
}
