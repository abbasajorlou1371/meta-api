<?php

namespace App\Console\Commands;

use App\Models\Feature\FeatureLimit;
use App\Models\FeatureProperties;
use Illuminate\Console\Command;

class RemoveFeatureLimits extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:remove-feature-limits';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove feature limits from the system.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        FeatureLimit::where('expired', false)->where('end_date', '<', now())->chunk(10, function ($featureLimits) {
            foreach ($featureLimits as $featureLimit) {
                $startId = explode('-', $featureLimit->start_id);
                $endId = explode('-', $featureLimit->end_id);

                FeatureProperties::where('id_prefix', $startId[0])
                    ->where('id_postfix', '>=', $startId[1])
                    ->where('id_postfix', '<=', $endId[1])
                    ->where('owner_id', 1)
                    ->chunk(100, function ($featureProperties) {
                        foreach ($featureProperties as $featureProperty) {
                            $featureProperty->update([
                                'stability' => $featureProperty->area * $featureProperty->density,
                                'rgb' => $this->getFeatureRGB($featureProperty),
                            ]);
                        }
                    });

                $featureLimit->update(['expired' => true]);
            }
        });

        return Command::SUCCESS;
    }

    private function getFeatureRGB(FeatureProperties $feature)
    {
        return match ($feature->karbari) {
            'm' => 'd',
            't' => 'k',
            'a' => 'r',
            default => 'rgb',
        };
    }
}
