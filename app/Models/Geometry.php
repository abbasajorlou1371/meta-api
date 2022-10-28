<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Geometry
 *
 * @property int $features_id
 * @property string|null $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Feature $feature
 * @property Collection|Coordinate[] $coordinates
 * @package App\Models
 * @property-read int|null $coordinates_count
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry query()
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry whereFeaturesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Geometry whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Geometry extends Model
{

	protected $fillable = [
		'type',
        'feature_id'
	];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

	public function feature()
	{
		return $this->belongsTo(Feature::class, 'feature_id');
	}

	public function coordinates()
	{
		return $this->hasMany(Coordinate::class , 'geometry_id', 'id');
	}
}
