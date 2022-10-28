<?php

namespace App\Models\Reset;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetEmail extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'code',
        'expires',
        'count',
        'user_id'
    ];
}
