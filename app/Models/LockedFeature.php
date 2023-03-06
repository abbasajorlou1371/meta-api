<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LockedFeature extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }
}
