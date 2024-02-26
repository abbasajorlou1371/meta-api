<?php

namespace App\Models\Feature;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Building extends Pivot
{
    protected $fillable = [
        'model_id',
        'feature_id',
        'construction_start_date',
        'construction_end_date',
        'launched_satisfaction',
        'information',
        'status',
    ];

    protected $casts = [
        'information' => 'array',
    ];

    public $timestamps = true;
}
