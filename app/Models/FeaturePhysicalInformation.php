<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeaturePhysicalInformation extends Model
{
    protected $table = 'feature_physical_information';

    protected $fillable = [
        'feature_id',
        'group_name',
        'active_company',
        'physical_address',
        'physical_postal_code',
        'postal_address',
        'establishment_goal',
        'isic_code_id',
    ];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function isicCode(): BelongsTo
    {
        return $this->belongsTo(IsicCode::class);
    }
}
