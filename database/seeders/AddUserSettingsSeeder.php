<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddUserSettingsSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::all()->map(function($user) {
            if(is_null($user->settings)) {
                $user->settings()->create();
            }

            if(is_null($user->generalSettings)) {
                $user->generalSettings()->create();
            }

            if(is_null($user->log)) {
                $user->log()->create();
            }

            if(is_null($user->variables)) {
                $user->variables()->create();
            }

            if(is_null($user->privacy)) {
                createUserPrivacy($user);
            }

            if(is_null($user->assets)) {
                $user->assets()->create();
            }
        });
    }
}
