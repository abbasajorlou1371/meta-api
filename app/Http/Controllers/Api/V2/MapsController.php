<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Http\Resources\V2\MapResource;
use App\Models\Map;

class MapsController extends Controller
{
    /**
     * Display a listing of maps.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return MapResource::collection(Map::with('features')->get());
    }

    /**
     * Display the specified map.
     *
     * @param  \App\Models\Map  $map
     * @return \Illuminate\Http\Response
     */
    public function show(Map $map)
    {
        $map = $map->load(['features', 'features.properties']);
        return new MapResource($map);
    }

    /**
     * Display the specified map's border coordinates.
     * @param Map $map
     * @return \Illuminate\Http\JsonResponse
     */
    public function border(Map $map)
    {
        return response()->json([
            'data' => [
                'border_coordinates' => $map->border_coordinates,
            ]
        ]);
    }
}
