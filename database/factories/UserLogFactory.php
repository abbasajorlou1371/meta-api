<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserLog>
 */
class UserLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => 11,
            'transactions_count' => 0,
            'followers_count' => 0,
            'deposit_amount' => 0,
            'activity_hours' => 0,
            'score' => 0,
        ];
    }
}
