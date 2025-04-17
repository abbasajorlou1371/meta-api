<?php

namespace App\Models\Dynasty;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JoinRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * @return BelongsTo
     */
    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(User::class,'from_user', 'id');
    }

    /**
     * @return BelongsTo
     */
    public function toUser(): BelongsTo
    {
        return $this->belongsTo(User::class,'to_user', 'id');
    }

    public function scopeLatestSentJoinRequest($query, $from_user, $to_user)
    {
        return $query->where('from_user', $from_user)->where('to_user', $to_user)->latest()->first();
    }

    public function getRelationShipTitle()
    {
        return match($this->relationship) {
            'brother' => 'برادر',
            'sister' => 'خواهر',
            'offspring' => 'فرزند',
            'father' => 'پدر',
            'mother' => 'مادر',
            'husband' => 'شوهر',
            'wife' => 'زن',
        };
    }
}
