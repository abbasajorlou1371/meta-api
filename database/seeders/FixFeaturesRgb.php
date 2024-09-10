<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FeatureProperties;

class FixFeaturesRgb extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $startId = 'to11-1';
        $endId = 'to11-4656';
        FeatureProperties::where('id_prefix', $startId[0])
            ->where('id_postfix', '>=', $startId[1])
            ->where('id_postfix', '<=', $endId[1])
            ->where('owner_id', '!=', 1)
            ->chunk(100, function ($featureProperties) {
                if ($featureProperties->feature->hasPendingRequests()) {
                    $featureProperties->update([
                        'rgb' => $this->pricedFeatureRgb($featureProperties)
                    ]);
                } else {
                    $featureProperties->update([
                        'rgb' => $this->notPricedFeatureRgb($featureProperties)
                    ]);
                }
            });
    }

    public function pricedFeatureRgb($featureProperties)
    {
        return match ($featureProperties->karbari) {
            'm' => 'a',
            't' => 'h',
            'a' => 'o',
            default => null,
        };
    }

    private function notPricedFeatureRgb(FeatureProperties $feature)
    {
        return match ($feature->karbari) {
            'm' => 'b',
            't' => 'i',
            'a' => 'p',
            default => 'rgb',
        };
    }
}
