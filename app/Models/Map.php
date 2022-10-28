<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Map
 *
 * @property int $id
 * @property string|null $type
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Collection|Cr[] $crs
 * @property Collection|Feature[] $features
 * @package App\Models
 * @property-read int|null $crs_count
 * @property-read int|null $features_count
 * @method static \Illuminate\Database\Eloquent\Builder|Map newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Map query()
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Map whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Map extends Model
{
	protected $table = 'maps';

	protected $fillable = [
		'type',
		'name'
	];

	public function crs()
	{
		return $this->hasMany(Cr::class);
	}

	public function features()
	{
		return $this->hasMany(Feature::class);
	}
}
