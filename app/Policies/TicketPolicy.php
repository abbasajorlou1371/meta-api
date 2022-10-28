<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TicketPolicy
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

    public function respond(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->reciever_id;
    }

    public function close(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id;
    }

    public function delete(User $user, Ticket $ticket)
    {
        return $user->id === $ticket->user_id;
    }


}
