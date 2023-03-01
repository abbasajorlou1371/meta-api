<?php

namespace App\Models\Dynasty;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynastyPrize extends Model
{
    use HasFactory;

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function getMemberTitle()
    {
        return match ($this->member) {
            'brother' => 'برادر',
            'sister' => 'خواهر',
            'offspring' => 'فرزند',
            'father' => 'پدر',
            'mother' => 'مادر',
            'husband' => 'شوهر',
            'wife' => 'زن',
        };
    }
}
