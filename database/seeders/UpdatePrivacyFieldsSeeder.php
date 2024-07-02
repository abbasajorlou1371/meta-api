<?php

namespace Database\Seeders;

use App\Models\Privacy;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdatePrivacyFieldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Privacy::where('name', 'birthdate')->update(['display' => false]);
    }
}
