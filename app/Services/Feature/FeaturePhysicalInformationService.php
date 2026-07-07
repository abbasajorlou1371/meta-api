<?php

namespace App\Services\Feature;

use App\Models\Feature;
use App\Models\FeaturePhysicalInformation;
use App\Models\IsicCode;
use Illuminate\Support\Facades\DB;

class FeaturePhysicalInformationService
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function upsert(Feature $feature, array $data): FeaturePhysicalInformation
    {
        return DB::transaction(function () use ($feature, $data) {
            $isicCode = $this->resolveIsicCode($data);

            return FeaturePhysicalInformation::updateOrCreate(
                ['feature_id' => $feature->id],
                [
                    'group_name' => $data['group_name'],
                    'active_company' => $data['active_company'],
                    'physical_address' => $data['physical_address'],
                    'physical_postal_code' => $data['physical_postal_code'],
                    'postal_address' => $data['postal_address'],
                    'establishment_goal' => $data['establishment_goal'],
                    'isic_code_id' => $isicCode->id,
                ],
            )->load('isicCode');
        });
    }

    public function get(Feature $feature): ?FeaturePhysicalInformation
    {
        return $feature->physicalInformation()
            ->with('isicCode:id,code,name,verified')
            ->first();
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function resolveIsicCode(array $data): IsicCode
    {
        if (! empty($data['isic_code_id'])) {
            return IsicCode::query()->findOrFail($data['isic_code_id']);
        }

        $activityName = trim((string) $data['activity']);

        return IsicCode::firstOrCreate(
            ['name' => $activityName],
            [
                'name' => $activityName,
                'verified' => false,
            ],
        );
    }
}
