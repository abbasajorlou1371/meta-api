<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttachPreviouslyAchievedLevelsToUser extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $levels = \App\Models\Levels\Level::all();

        \App\Models\User::chunk(100, function ($users) use ($levels) {
            foreach ($users as $user) {
                $previousLevels = $levels->where('score', '<=', $user->score);
                $user->levels()->sync($previousLevels->pluck('id'));
            }
        });
    }
}
