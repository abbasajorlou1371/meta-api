<?php

namespace App\Constants;
use Illuminate\Validation\Rules\Enum;

class TicketStatus extends Enum
{
    const NEW = 0;
    const ANSWERED = 1;
    const TRACKING = 2;
    const CLOSED = 3;
}
