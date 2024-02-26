<?php

namespace App\Models\Feature;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuildingModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_id',
        'name',
        'sku',
        'images',
        'attributes',
        'file',
        'required_satisfaction',
    ];

    protected $casts = [
        'images' => 'array',
        'attributes' => 'array',
        'file' => 'array',
    ];

    public function feature()
    {
        // return $this->belongsTo(Feature::class);
    }
}
