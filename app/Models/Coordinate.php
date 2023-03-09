<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coordinate extends Model
{
	protected $guarded = [];

    protected $casts = [
        'x' => 'decimal:12',
        'y' => 'decimal:12',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

	public function geometry()
	{
		return $this->belongsTo(Geometry::class, 'geometry_id', 'id');
	}

    public function implodeXY() {
        return implode(',', [$this->x, $this->y]);
    }
}
