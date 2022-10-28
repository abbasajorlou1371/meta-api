<?php

namespace App\Models\Dynasty;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'family_id',
        'relationship',
    ];

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }
}
