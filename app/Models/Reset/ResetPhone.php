<?php

namespace App\Models\Reset;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetPhone extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone', 'code', 'user_id', 'count', 'expires'
    ];
}
