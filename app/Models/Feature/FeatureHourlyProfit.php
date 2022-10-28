<?php

namespace App\Models\Feature;

use App\Models\Feature;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureHourlyProfit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'feature_id',
        'asset',
        'amount',
        'dead_line'
    ];


    public function feature() {
        return $this->belongsTo(Feature::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
