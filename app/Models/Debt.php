<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Debt extends Model
{
    use HasFactory;

    public $fillable = [
        'user_id',
        'irr',
        'psc',
        'yellow',
        'red',
        'blue',
        'status',
        'reason'
    ];

    public function debtable()
    {
        return $this->morphTo();
    }
}
