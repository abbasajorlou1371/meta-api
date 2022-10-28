<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class CrsProperty
 *
 * @property int $id
 * @property int $crs_id
 * @property string|null $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Cr $cr
 * @package App\Models
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty query()
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty whereCrsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CrsProperty whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class CrsProperty extends Model
{
	protected $table = 'crs_properties';
	public $incrementing = false;

	protected $casts = [
		'id' => 'int',
		'cr_id' => 'int'
	];

	protected $fillable = [
		'cr_id',
		'name'
	];

	public function cr()
	{
		return $this->belongsTo(Cr::class, 'crs_id');
	}
}
