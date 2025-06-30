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
        // Get IDs of features processed in the first group
        $processedFeatureIds = [];

        Feature::with('properties')
            ->whereHas('sellRequests', function ($query) {
                $query->where('status', 0);
            })
            ->chunkById(100, function ($features) use (&$processedFeatureIds) {
                foreach ($features as $feature) {
                    $feature->properties->update([
                        'rgb' => $feature->changeStatusToSoldAndPriced(),
                    ]);
                    $processedFeatureIds[] = $feature->id;
                }
            });

        Feature::with('properties')
            ->where('owner_id', '!=', 1)
            ->whereNotIn('id', $processedFeatureIds)  // Exclude already processed features
            ->chunkById(100, function ($features) {
                foreach ($features as $feature) {
                    $feature->properties->update([
                        'rgb' => $feature->changeStatusToSoldAndNotPriced(),
                    ]);
                }
            });
    }
}
