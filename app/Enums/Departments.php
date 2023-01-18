<?php

namespace App\Enums;

enum Departments:string {
    case TECKNICALSUPPORT = 'technical_support';
    case CITIZENSSAFETY = 'citizens_safety';
    case INVESTMENT = 'investment';
    case INSPECTION = 'inspection';
    case PROTECTION = 'protection';
    case ZTB = 'ztb';
}
