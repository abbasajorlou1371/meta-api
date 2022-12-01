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

    public static function requestStatus($status)
    {
        switch($status) {
            case self::WAITING:
                return 'در انتظار تایید';
                break;
            case self::ACCEPTED:
                return 'پذیرفته شده';
                break;
            case self::PENDING:
                return 'ارسال نشده';
                break;
            case self::CANCELED:
                return 'لغو شده';
                break;
            case self::REJECTED:
                return 'رد شده';
                break;
        }
    }
}
