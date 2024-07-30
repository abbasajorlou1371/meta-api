<?php

namespace App\Models\Levels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RecievedLevelPrize extends Pivot
{
    use HasFactory;

    protected $guarded = [];
}
