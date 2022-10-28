<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Coordinate
 *
 * @property int $id
 * @property int $geometry_id
 * @property string|null $x
 * @property string|null $y
 * @property string|null $index
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Geometry $geometry
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate query()
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereGeometryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereIndex($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereX($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Coordinate whereY($value)
 * @mixin \Eloquent
 */
class Coordinate extends Model
{

	protected $fillable = [
		'geometry_id',
		'x',
		'y',
	];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

	public function geometry()
	{
		return $this->belongsTo(Geometry::class, 'geometry_id', 'id');
	}
}
