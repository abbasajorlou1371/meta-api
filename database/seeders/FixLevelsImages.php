<?php

namespace Database\Seeders;

use App\Models\Image;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixLevelsImages extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Image::where('imageable_type', 'App\Models\Level\Level')->chunk(100, function ($images) {
            foreach ($images as $image) {
                $image->update([
                    'imageable_type' => 'App\Models\Levels\Level',
                ]);
            }
        });
    }
}
