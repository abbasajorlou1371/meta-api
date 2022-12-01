<?php

namespace App\Models\Feature;

use App\Models\Otp;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureOtp extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'otp_off',
        'time'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function otp()
    {
        return $this->morphOne(Otp::class, 'verifiable');
    }
}
