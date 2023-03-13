<?php

namespace App\Repositories;

use App\Models\Coordinate;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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

    public function all(Request $request)
    {
        $request->validate([
            'points' => 'required|array|min:4',
            'points.*' => 'required|regex:/^([0-9]+(\.[0-9]+)?,[0-9]+(\.[0-9]+)?)$/'
        ]);

        for ($i = 0; $i < count($request->points); $i++) {
            $points[$i] = explode(',', $request->points[$i]);
        }

        $existingGeometries = Coordinate::whereBetween('x', [
            $points[0][0],
            $points[1][0]
        ])
            ->whereBetween('y', [
                $points[0][1],
                $points[2][1]
            ])
            ->distinct('geometry_id')
            ->pluck('geometry_id');

        return Feature::whereIn('id', $existingGeometries)
            ->selectRaw('id, owner_id as owner')
            ->with('properties:id,feature_id,rgb', 'geometry.coordinates:id,geometry_id,x,y')
            ->lazy();
    }
}
