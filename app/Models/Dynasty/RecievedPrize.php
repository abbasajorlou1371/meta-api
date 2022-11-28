<?php

namespace App\Models\Dynasty;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecievedPrize extends Model
{
    use HasFactory;

    protected $table = 'received_prizes';

    protected $fillable = ['user_id', 'prize_id', 'message'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prize()
    {
        return $this->belongsTo(DynastyPrize::class, 'prize_id', 'id');
    }
}
