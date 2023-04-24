<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = ['url'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function url():Attribute
    {
        return Attribute::make(
            get: fn($value) => config('rgb.admin_panel_url') . $value
        );
    }
}
