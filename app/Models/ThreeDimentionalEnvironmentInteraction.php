<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Feature\ThreeDimentionalEnvironment;

class ThreeDimentionalEnvironmentInteraction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'three_dimentional_environment_id',
        'user_id',
        'entered_at',
        'exited_at',
        'duration',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'entered_at' => 'datetime',
            'exited_at' => 'datetime',
            'duration' => 'integer',
        ];
    }

    /**
     * Get the three dimentional environment that owns the ThreeDimentionalEnvironmentInteraction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function threeDimentionalEnvironment()
    {
        return $this->belongsTo(ThreeDimentionalEnvironment::class);
    }

}
