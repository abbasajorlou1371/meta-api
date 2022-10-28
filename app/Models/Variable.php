<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Variable
 *
 * @property int $id
 * @property string $title
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Variable newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Variable newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Variable query()
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Variable whereValue($value)
 * @mixin \Eloquent
 */
class Variable extends Model
{
    use HasFactory;
    protected $table = "variables";

    protected $fillable = [
        'asset',
        'price',
    ];

    public static function getRate($asset) {
        return self::firstWhere('asset', $asset)->price;
    }
}
