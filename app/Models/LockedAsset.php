<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockedAsset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'buy_feature_request_id',
        'feature_id',
        'psc',
        'irr',
    ];

    public function assetable() {
        return $this->morphTo();
    }
}
