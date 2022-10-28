<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Trade;

class Comission extends Model
{
    use HasFactory;

    protected $fillable = [
        'trade_id',
        'psc',
        'irr'
    ];

    public function trade() {
        return $this->belongsTo(Trade::class);
    }
}
