<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Level\Level;
use App\Models\User;
use App\Models\UserLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            UserSeeder::class,
            LevelSeeder::class,
            UserLogSeeder::class,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'rgb',
            'email' => 'rgb@rgb.com',
            'password' => bcrypt('123456'),
//            'ip' => '127.0.0.1',
            'phone' => '09127855049',
//            'last_activity' => (string)Carbon::now()->format('Y:m:d h:m:s')
        ]);
        // $levelIds = [];
        // $user = \App\Models\User::where('email','rgb@rgb.com')->first();
        // $level = \App\Models\Level\Level::first();
        // array_push($levelIds,$level->code);
        // $user->level()->attach($levelIds);

        $users = User::all();

        foreach ($users as $user)
        {
            UserLog::create([
                'user_id' => $user->id,
                'transactions_count' => 0,
                'followers_count' => 0,
                'deposit_amount' => 0,
                'activity_hours' => 0,
                'score' => 0,
            ]);
        }
    }
}
