<?php

namespace App\Http\Controllers\Api\V2\Feature;

use App\Http\Controllers\Controller;
use App\Models\Feature;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Http;
use App\Models\Feature\BuildingModel;
use Illuminate\Support\Facades\DB;

class BuildFeatureController extends Controller
{
    public function getBuildPackage(Feature $feature)
    {
        $feature->load('properties:id,feature_id,area,density,karbari', 'owner:id', 'coordinates');

        throw_unless($feature->owner->id === auth()->id(), AuthorizationException::class);

        $query = http_build_query([
            'feature_id' => $feature->id,
            'area' => $feature->properties->area,
            'density' => $feature->properties->density,
            'karbari' => $feature->properties->karbari,
            'page' => request('page', 1),
        ]);

        $url = config('app.three_d_meta_url') . '/api/v1/build-package';

        $response = $this->sendRequest($url, $query);

        $response = $this->calculateRequiredSatisfaction($feature, $response);
        $response = $this->mergeCoordinates($feature, $response);

        // $data = $response['data'];

        // $this->updateOrCreateModels($data);

        return response()->json($response);
    }

    private function calculateRequiredSatisfaction(Feature $feature, array $data)
    {
        foreach ($data['data'] as &$item) {
            $attributes = $item['attributes'];

            $area = collect($attributes)->firstWhere('slug', 'area')['value'];
            $density = collect($attributes)->firstWhere('slug', 'density')['value'];

            $item['required_satisfaction'] = number_format($area * $feature->getKarbariCoefficient() * $density * 0.1 / 100, 4);
        }

        return $data;
    }

    private function updateOrCreateModels(array $data): void
    {
        DB::transaction(function () use ($data) {
            $models = [];
            foreach ($data as $item) {
                $models[] = [
                    'model_id' => $item['id'],
                    'name' => $item['name'],
                    'sku' => $item['sku'],
                    'images' => $item['images'],
                    'attributes' => $item['attributes'],
                    'file' => $item['file'],
                    'required_satisfaction' => $item['required_satisfaction'],
                ];
            }

            DB::table('building_models')->upsert($models, ['model_id'], [
                'name',
                'sku',
                'images',
                'attributes',
                'file',
                'required_satisfaction',
            ]);
        });
    }

    private function sendRequest(string $url, $query = null)
    {
        try {
            $response = Http::get($url, $query);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error in sending request to 3D Meta API.',
                'error' => $e->getMessage(),
            ], 500);
        }

        return $response->json();
    }

    private function mergeCoordinates(Feature $feature, array $response)
    {
        $coordinates = $feature->coordinates->map(function ($coordinate) {
            return $coordinate->implodeXY();
        });

        $response['feature']['coordinates'] = $coordinates;

        return $response;
    }
}
