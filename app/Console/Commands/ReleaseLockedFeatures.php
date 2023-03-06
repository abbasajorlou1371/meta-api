<?php

namespace App\Console\Commands;

use App\Models\LockedFeature;
use Illuminate\Console\Command;

class ReleaseLockedFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:release-feature';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Releases features if their locked perioud passed';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        LockedFeature::where('until', '<', now())
            ->lazyById()
            ->map(function($lockedFeature) {
                $feature = $lockedFeature->feature;
                $feature->properties->update(['locked' => '']);
                $lockedFeature->update(['status' => 1]);
            });
        return Command::SUCCESS;
    }
}
