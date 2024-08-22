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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The "type" of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status' => 'integer',
        'token' => 'integer',
        'ref_id' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 1
    ];

    /**
     * Get the user who made the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the payable model for the transaction.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function payable()
    {
        return $this->morphTo();
    }

    /**
     * Get the title of the transaction based on the payable model.
     *
     * @return string
     */
    public function newUniqueId()
    {
        return (string) $this->generateId();
    }

    /**
     * Generate a new unique ID for the transaction.
     *
     * @return string
     */
    private function generateId(): string
    {
        do {
            $id = 'TR-' . random_int(100000000, 999999999);
        } while (self::where('id', $id)->exists());

        return $id;
    }


    /**
     * Get the title of the transaction based on the payable model.
     *
     * @return string
     */
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

    /**
     * Get the title of the transaction based on the payable model.
     *
     * @return string
     */
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
