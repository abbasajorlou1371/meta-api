<?php

namespace App\Models\Level;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecievedLevelPrize extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'prize_id'];
}
