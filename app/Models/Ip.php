<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ip extends Model
{
    use HasFactory;

    protected $fillable = [
        'from',
        'to',
        'type',
        'email',
        'blocked',
        'title'
    ];

    protected $casts = [
        'from' => 'integer',
        'to' => 'integer',
    ];

    public function scopeFree($query)
    {
        return $query->where('blocked', 0);
    }
}
