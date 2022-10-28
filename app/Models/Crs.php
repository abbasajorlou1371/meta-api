<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cr
 *
 * @property int $id
 * @property int $map_id
 * @property string|null $type
 * @property Map $map
 * @property Collection|CrsProperty[] $crs_properties
 * @package App\Models
 * @property-read int|null $crs_properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|Cr newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cr newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Cr query()
 * @method static \Illuminate\Database\Eloquent\Builder|Cr whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cr whereMapId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Cr whereType($value)
 * @mixin \Eloquent
 */
class Crs extends Model
{
	protected $table = 'crs';
	public $timestamps = false;

	protected $casts = [
		'map_id' => 'int'
	];

	protected $fillable = [
		'map_id',
		'type'
	];

	public function map()
	{
		return $this->belongsTo(Map::class);
	}

	public function crs_properties()
	{
		return $this->hasMany(CrsProperty::class, 'crs_id');
	}
}
