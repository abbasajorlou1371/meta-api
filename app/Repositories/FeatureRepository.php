<?php

namespace App\Repositories;

use App\Models\Coordinate;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
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

        return Coordinate::whereBetween('x', [
            $points[0][0],
            $points[1][0]
        ])
        ->whereBetween('y', [
            $points[0][1],
            $points[2][1]
        ])
        ->leftjoin('geometries', function (JoinClause $join) {
            $join->on('coordinates.geometry_id', '=', 'geometries.id');
        })
        ->leftjoin('features', function (JoinClause $join) {
            $join->on('geometries.feature_id', '=', 'features.id');
        })
        ->lazy()
        ->map(function ($feature) {
            return [
                'id' => $feature->id,
                'x' => $feature->x,
                'y' => $feature->y,
            ];
        });
    }
}
