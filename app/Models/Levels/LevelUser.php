<?php

namespace App\Models\Levels;

use Illuminate\Database\Eloquent\Relations\Pivot;

class LevelUser extends Pivot
{
    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;
}
