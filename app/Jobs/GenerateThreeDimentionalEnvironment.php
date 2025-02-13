<?php

namespace App\Jobs;

use App\Models\Feature;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class GenerateThreeDimentionalEnvironment implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Feature::with(['buildingModels', 'properties'])
            ->whereHas('buildingModels', function ($query) {
                $query->wherePivot('construction_end_date', '<', now());
            })
            ->chunk(100, function ($features) {
                $features->each(function ($feature) {
                    $feature->buildingModels->each(function ($buildingModel) use ($feature) {
                        if ($buildingModel->construction_end_date < now()) {
                            $this->generateThreeDimentionalEnvironment($feature, $buildingModel);
                        }
                    });
                });
            });
    }

    /**
     * Generate three dimentional environment.
     *
     * @param $buildingModel
     */
    private function generateThreeDimentionalEnvironment($feature, $buildingModel): void
    {
        $url = 'https://3ddevelop.irpsc.com/webserver/environmentdata';

        $params = json_encode([
            'id' => $feature->properties->id,
            'type' => $feature->karbari,
            'radius' => $buildingModel->bubble_diameter,
        ]);

        $response = $this->sendRequest($url, $params);

        if ($response->successful()) {
            $response = $response->body();

            $buildingModel->update([
                'three_dimentional_environment' => $response,
            ]);
        }
    }

    /**
     * Send request to 3d server.
     *
     * @param $url
     * @param $params
     * @return mixed
     */
    private function sendRequest($url, $params)
    {
        return Http::post($url, $params);
    }
}
