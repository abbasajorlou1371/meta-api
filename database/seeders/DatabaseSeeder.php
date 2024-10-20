<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $user->update([
                    'email_verified_at' => $user->created_at,
                ]);
            }
        });
    }
}
