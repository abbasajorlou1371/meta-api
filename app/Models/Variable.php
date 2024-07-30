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

    protected $casts = [
        'price' => 'float',
    ];

    /**
     * Get the rate for a specific asset.
     *
     * @param string $asset The asset name.
     * @return float The rate of the asset.
     */
    public static function getRate($asset)
    {
        return self::firstWhere('asset', $asset)->price;
    }

    /**
     * Get the image associated with the variable.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }
}
