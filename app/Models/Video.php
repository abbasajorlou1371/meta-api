<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected function fileName(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('rgb.admin_panel_url').$value
        );
    }

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => config('rgb.admin_panel_url').$value
        );
    }
}
