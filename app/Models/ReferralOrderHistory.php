<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferralOrderHistory extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['user_id', 'referral_id', 'amount'];

    /**
     * Get the user that owns the referral order history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the referral that owns the referral order history.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function referral()
    {
        return $this->belongsTo(User::class, 'referral_id');
    }
}
