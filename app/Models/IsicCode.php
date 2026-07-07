<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IsicCode extends Model
{
    use HasFactory;

    protected $table = 'isic_codes';

    protected $fillable = [
        'code',
        'name',
        'verified',
    ];

    protected function casts(): array
    {
        return [
            'verified' => 'boolean',
        ];
    }

    public function physicalInformation(): HasMany
    {
        return $this->hasMany(FeaturePhysicalInformation::class);
    }
}
