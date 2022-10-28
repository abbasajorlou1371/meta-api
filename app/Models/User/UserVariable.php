<?php

namespace App\Models\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVariable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'referral_profit',
        'data_storage',
        'withdraw_profit'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
