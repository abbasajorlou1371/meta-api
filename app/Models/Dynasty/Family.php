<?php

namespace App\Models\Dynasty;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'dynasty_id'
    ];

    /**
     * @return BelongsTo
     */
    public function dynasty(): BelongsTo
    {
        return $this->belongsTo(Dynasty::class);
    }

    /**
     * @return HasMany
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }
}
