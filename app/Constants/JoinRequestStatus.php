<?php

namespace App\Constants;

class JoinRequestStatus extends Enum
{
    const WAITING = "1";
    const ACCEPTED = "2";
    const REJECTED = "3";
    const CANCELED = "4";
    const PENDING = "5";
    const RejectedBySupport="6";
    const AcceptedBySupport = "7";
}
