<?php

namespace App\Models\Dynasty;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class childrenPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'verified',
        'BFR',
        'SF',
        'W',
        'JU',
        'DM',
        'PIUP',
        'PITC',
        'PIC',
        'ESOO',
        'COTB'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
