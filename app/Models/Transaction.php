<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $keyType = 'string';
    public $incrementing = false;

    protected $attributes = [
        'status' => 0
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payable()
    {
        return $this->morphTo();
    }

    public function newUniqueId()
    {
        return (string) $this->generateId();
    }

    private function generateId(): string
    {
        do {
            $id = 'TR-' . random_int(100000000, 999999999);
        } while (self::where('id', $id)->exists());

        return $id;
    }


    protected function asset(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                return match ($value) {
                    'blue' => 'رنگ آبی',
                    'yellow' => 'رنگ زرد',
                    'red' => 'رنگ قرمز',
                    'psc' => 'PSC',
                    'irr' => 'ریال',
                };
            }
        );
    }

    public function getTitle()
    {
        if ($this->payable instanceof BuyFeatureRequest) {
            return 'پیشنهاد خرید ملک';
        } elseif ($this->payable instanceof Trade) {
            return 'معامله ملک';
        } elseif ($this->payable instanceof Order) {
            return 'خرید دارایی';
        }
    }
}
