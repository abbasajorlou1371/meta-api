<?php

namespace App\Policies;

use App\Models\Dynasty\Dynasty;
use App\Models\Feature;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class DynastyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dynasty  $dynasty
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Dynasty $dynasty)
    {
        //
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        if(! $user->verified()) {
            return Response::deny('شما برای تاسیس سلسله باید احراز هویت مرحله 2 را انجام دهید', 403);
        }

        if(! empty($user->dynasty)) {
            return Response::deny('شما در حال حاظر سلسله دارید و مجاز به تاسیس سلسله جدید نیستید', 403);
        }
        return true;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dynasty  $dynasty
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Dynasty $dynasty)
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dynasty  $dynasty
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Dynasty $dynasty)
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dynasty  $dynasty
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function restore(User $user, Dynasty $dynasty)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Dynasty  $dynasty
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function forceDelete(User $user, Dynasty $dynasty)
    {
        //
    }
}
