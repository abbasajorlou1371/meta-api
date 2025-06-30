<?php

namespace Database\Seeders;

use App\Models\Feature;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FixFeatureRgbSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Feature::with('properties')
            ->whereHas('sellRequests', function ($query) {
                $query->where('status', 0);
            })
            ->chunkById(100, function ($features) {
                foreach ($features as $feature) {
                    $feature->properties->update([
                        'rgb' => $feature->changeStatusToSoldAndPriced(),
                    ]);
                }
            });

        Feature::with('properties')
            ->where('owner_id', '!=', 1)
            ->chunkById(100, function ($features) {
                foreach ($features as $feature) {
                    $feature->properties->update([
                        'rgb' => $feature->changeStatusToSoldAndNotPriced(),
                    ]);
                }
            });
    }
}
