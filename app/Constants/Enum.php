<?php

namespace App\Constants;

use ReflectionClass;

abstract class Enum
{
    public static function toArray(): array
    {
        return (new ReflectionClass(static::class))->getConstants();
    }
}
