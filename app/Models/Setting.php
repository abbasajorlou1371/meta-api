<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'automatic_logout' => 'integer'
    ];

    protected $attributes = [
        'automatic_logout' => 60
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
