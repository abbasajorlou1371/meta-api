<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileLimitation extends Model
{
    use HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'limiter_user_id',
        'limited_user_id',
        'options',
        'note',
    ];

    /**
     * The default attribute values.
     *
     * @var array
     */
    protected $attributes = [
        'options' => '{
            "follow": 1,
            "send_message": 1,
            "share": 1,
            "send_ticket": 1,
            "view_profile_images": 1,
            "view_features_locations": 1,
        }',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'options' => 'array',
    ];

    /**
     * Get the user who set the limit.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function limiterUser()
    {
        return $this->belongsTo(User::class, 'limiter_user_id');
    }

    /**
     * Get the user who is limited.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function limitedUser()
    {
        return $this->belongsTo(User::class, 'limited_user_id');
    }
}
