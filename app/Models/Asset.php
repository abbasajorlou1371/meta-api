<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $attributes = [
        'effect' => 0,
    ];

    public function format_number($number): string
    {
        if ($number >= 1000 && $number < 1000000) {
            $number = number_format($number / 1000, ($number * 1000) % 1000 > 0 ? 3 : 0);
            return $number . 'K';
        } elseif ($number >= 1000000 && $number < 1000000000) {
            $number = number_format($number / 1000000, ($number * 1000000) % 1000000 > 0 ? 3 : 0);
            return $number . 'M';
        } elseif ($number < 1000) {
            $number = number_format($number, ($number * 1000) % 1000 > 0 ? 3 : 0);
            return $number;
        }
    }
}
