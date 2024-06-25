<?php

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\GeneralSetting  $generalSetting
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Setting $setting)
    {
        return $setting->user->is($user);
    }
}
