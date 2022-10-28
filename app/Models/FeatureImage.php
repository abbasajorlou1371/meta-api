<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FeatureImage extends Model
{
	protected $casts = [
		'feature_id' => 'int',
	];

	protected $fillable = [
		'feature_id',
        'url'
	];

	public function feature()
	{
		return $this->belongsTo(Feature::class);
	}
}
