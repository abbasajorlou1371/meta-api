<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */

    public function canGetBonus(User $user, Order $order) {
        return  is_null($user->firstOrder) && $order->asset !== 'irr';
    }
}
