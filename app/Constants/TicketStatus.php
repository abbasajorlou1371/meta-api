<?php

namespace App\Constants;
use Illuminate\Validation\Rules\Enum;

class TicketStatus extends Enum
{
    const NEW = 0;
    const ANSWERED = 1;
    const RESOLVED = 2;
    const UNRESOLVED = 3;
    const TRACKING = 4;
    const CLOSED = 5;
}
