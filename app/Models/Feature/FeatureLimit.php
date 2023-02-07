<?php

namespace App\Models\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureLimit extends Model
{
    use HasFactory;

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];
}
