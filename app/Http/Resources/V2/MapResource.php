<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class MapResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'color' => $this->polygon_color,
            'sold_features_percentage' => $this->features->where('owner_id', '<>', 1)->count() / $this->features->count() * 100,
            $this->mergeWhen(request()->routeIs('maps.show'), [
                'central_point_coordinates' => $this->central_point_coordinates,
                'border_coordinates' => $this->border_coordinates,
                'area' => $this->polygon_area,
                'address' => $this->polygon_address,
                'published_at' => $this->publish_date,
                'features' => [
                    'maskoni' => [
                        'sold' => $this->features->where('owner_id', '<>', 1)->where(function ($query) {
                            $query->select('karbari')
                                ->from('feature_properties')
                                ->whereColumn('features.id', 'feature_properties.feature_id')
                                ->limit(1);
                        }, 'm')->count(),

                        'has_dynasty' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 'm')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('dynasties')
                                    ->whereColumn('dynasties.feature_id', 'features.id');
                            }, '!=', null)->count(),

                        'priced' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 'm')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('sell_feature_requests')
                                    ->whereColumn('sell_feature_requests.feature_id', 'features.id')
                                    ->where('sell_feature_requests.status', '=', '0');
                            }, '!=', null)->count(),

                        'trades' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 'm')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('trades')
                                    ->whereColumn('trades.feature_id', 'features.id');
                            }, '!=', null)->count(),
                    ],
                    'tejari' => [
                        'sold' => $this->features->where('owner_id', '<>', 1)->where(function ($query) {
                            $query->select('karbari')
                                ->from('feature_properties')
                                ->whereColumn('features.id', 'feature_properties.feature_id')
                                ->limit(1);
                        }, 't')->count(),

                        'priced' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 't')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('sell_feature_requests')
                                    ->whereColumn('sell_feature_requests.feature_id', 'features.id')
                                    ->where('sell_feature_requests.status', '=', '0');
                            }, '!=', null)->count(),

                        'trades' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 't')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('trades')
                                    ->whereColumn('trades.feature_id', 'features.id');
                            }, '!=', null)->count(),
                    ],
                    'amoozeshi' => [
                        'sold' => $this->features->where('owner_id', '<>', 1)->where(function ($query) {
                            $query->select('karbari')
                                ->from('feature_properties')
                                ->whereColumn('features.id', 'feature_properties.feature_id')
                                ->limit(1);
                        }, 'a')->count(),

                        'priced' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 'a')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('sell_feature_requests')
                                    ->whereColumn('sell_feature_requests.feature_id', 'features.id')
                                    ->where('sell_feature_requests.status', '=', '0');
                            }, '!=', null)->count(),

                        'trades' => $this->features
                            ->where(function ($query) {
                                $query->select('karbari')
                                    ->from('feature_properties')
                                    ->whereColumn('features.id', 'feature_properties.feature_id')
                                    ->limit(1);
                            }, 'a')
                            ->where(function ($query) {
                                $query->select(DB::raw(1))
                                    ->from('trades')
                                    ->whereColumn('trades.feature_id', 'features.id');
                            }, '!=', null)->count(),
                    ],
                ]
            ]),
        ];
    }
}
