<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variable extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset',
        'price',
    ];

    public static function getRate($asset)
    {
        return self::firstWhere('asset', $asset)->price;
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
