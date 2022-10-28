<?php

namespace Database\Factories;

use App\Models\Level\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Prize>
 */
class PrizeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $level = Level::first();
        return [
            'level_code' => $level->code,
            'psc' => 1000,
            'blue' => 1000,
            'red' => 1000,
            'yellow' => 1000,
            'union_license' => true,
            'union_members_count' => 20,
            'observing_license' => true,
            'gate_license' => true,
            'lawyer_license' => true,
            'city_counsil_entry' => true,
            'special_residence_property' => true,
            'property_on_area' => true,
            'judge_entry' => 2,
            'satisfaction' => 1.2,
            'effect' => 2
        ];
    }
}
