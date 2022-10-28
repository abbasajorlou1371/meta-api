<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Land
 *
 * @property int $id
 * @property string $color
 * @property string|null $price
 * @property string|null $sku
 * @property string $area
 * @property string $density
 * @property string $type
 * @property string $address
 * @property int $registrar
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Land newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Land newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Land query()
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereArea($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereDensity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereRegistrar($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereSku($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Land whereUserId($value)
 * @mixin \Eloquent
 */
class Land extends Model
{
    use HasFactory;
    protected $table = "lands";
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
