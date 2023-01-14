<?php

namespace App\Repositories;

use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Morilog\Jalali\Jalalian;
class FeatureRepository extends Repository
{
    public function getByIds(array $ids): Collection
    {
        return Feature::find($ids);
    }

    public function getOneById($id): Model
    {
        return Feature::find($id);
    }

    public function getHomePageFeatures(): LazyCollection
    {
        return Feature::with(['properties', 'geometry', 'geometry.coordinates'])->lazyById()->map(function ($feature) {
            return [
                'id'         => $feature->id,
                'owner_id'   => $feature->owner_id,
                'properties' => [
                    'id'                       => $feature->properties->id,
                    'address'                  => $feature->properties->address,
                    'density'                  => $feature->properties->density,
                    'stability'                => $feature->properties->stability,
                    'label'                    => $feature->properties->label,
                    'area'                     => $feature->properties->area,
                    'region'                   => $feature->properties->region,
                    'karbari'                  => $feature->properties->karbari,
                    'owner'                    => $feature->properties->owner,
                    'rgb'                      => $feature->properties->rgb,
                    'price_psc'                => $feature->properties->price_psc,
                    'price_irr'                => $feature->properties->price_irr,
                    'minimum_price_percentage' => $feature->properties->minimum_price_percentage,
                    'created_at'               => Jalalian::forge($feature->properties->created_at)->format('Y/m/d'),
                ],
                'geometry'  => [
                    'type'        => $feature->geometry->type,
                    'coordinates' => $feature->geometry->coordinates->map(function ($coordinate) {
                        return [
                            'x' => $coordinate->x,
                            'y' => $coordinate->y
                        ];
                    })
                ]
            ];
        });
    }
}
