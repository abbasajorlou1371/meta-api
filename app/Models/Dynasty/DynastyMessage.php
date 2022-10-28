<?php

namespace App\Models\Dynasty;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynastyMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'message'
    ];
}
