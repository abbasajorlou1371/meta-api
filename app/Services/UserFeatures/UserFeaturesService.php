<?php

namespace App\Services\UserFeatures;

use App\Helpers\FeatureIndicators;
use App\Models\Feature;
use App\Models\Trade;
use App\Models\User;
use App\Services\WalletHistory\PeriodResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class UserFeaturesService
{
    public const KARBARI_PRIVACY_MAP = [
        FeatureIndicators::Amozeshi => 'amoozeshi_features',
        FeatureIndicators::Maskoni => 'maskoni_features',
        FeatureIndicators::Tejari => 'tejari_features',
        FeatureIndicators::Gardeshgari => 'gardeshgari_features',
        FeatureIndicators::FazaSabz => 'fazasabz_features',
        FeatureIndicators::Behdashti => 'behdashti_features',
        FeatureIndicators::Edari => 'edari_features',
        FeatureIndicators::Nemayeshgah => 'nemayeshgah_features',
    ];

    public const DISPLAYABLE_KARBARIS = [
        FeatureIndicators::Amozeshi,
        FeatureIndicators::Maskoni,
        FeatureIndicators::Tejari,
        FeatureIndicators::Gardeshgari,
        FeatureIndicators::FazaSabz,
        FeatureIndicators::Behdashti,
        FeatureIndicators::Edari,
        FeatureIndicators::Nemayeshgah,
    ];

    public function __construct(
        private readonly PeriodResolver $periodResolver,
    ) {
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     * @return array<string>
     */
    public function getEffectiveKarbaris(User $user, ?array $requestedKarbaris = null): array
    {
        $privacy = $user->settings?->privacy ?? [];
        $candidates = $requestedKarbaris ?: self::DISPLAYABLE_KARBARIS;

        return array_values(array_filter(
            $candidates,
            fn (string $karbari) => ($privacy[self::KARBARI_PRIVACY_MAP[$karbari] ?? ''] ?? 1) == 1
        ));
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     * @return array{data: array<int, array<string, mixed>>, period: string}
     */
    public function getSummary(User $user, string $period, ?array $requestedKarbaris = null): array
    {
        $karbaris = $this->getEffectiveKarbaris($user, $requestedKarbaris);
        $window = $this->periodResolver->resolve($period);

        $data = collect($karbaris)->map(function (string $karbari) use ($user, $window) {
            return [
                'karbari' => $karbari,
                'label' => $this->getKarbariLabel($karbari),
                'current_count' => Feature::query()
                    ->where('owner_id', $user->id)
                    ->whereHas('properties', fn ($query) => $query->where('karbari', $karbari))
                    ->count(),
                'bought_count' => Trade::query()
                    ->where('buyer_id', $user->id)
                    ->whereBetween('created_at', [$window['start'], $window['end']])
                    ->whereHas('feature.properties', fn ($query) => $query->where('karbari', $karbari))
                    ->count(),
                'sold_count' => Trade::query()
                    ->where('seller_id', $user->id)
                    ->whereBetween('created_at', [$window['start'], $window['end']])
                    ->whereHas('feature.properties', fn ($query) => $query->where('karbari', $karbari))
                    ->count(),
            ];
        })->values()->all();

        return [
            'data' => $data,
            'period' => $period,
        ];
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     * @return array{data: array{labels: array<int, string>, bought: array<int, int>, sold: array<int, int>}}
     */
    public function getChart(User $user, string $period, ?array $requestedKarbaris = null): array
    {
        $karbaris = $this->getEffectiveKarbaris($user, $requestedKarbaris);
        $window = $this->periodResolver->resolve($period);

        $labels = [];
        $bought = [];
        $sold = [];

        if ($karbaris === []) {
            foreach ($window['buckets'] as $bucket) {
                $labels[] = $bucket['label'];
                $bought[] = 0;
                $sold[] = 0;
            }

            return [
                'data' => [
                    'labels' => $labels,
                    'bought' => $bought,
                    'sold' => $sold,
                ],
            ];
        }

        $boughtTrades = Trade::query()
            ->where('buyer_id', $user->id)
            ->whereBetween('created_at', [$window['start'], $window['end']])
            ->whereHas('feature.properties', fn ($query) => $query->whereIn('karbari', $karbaris))
            ->get(['id', 'created_at']);

        $soldTrades = Trade::query()
            ->where('seller_id', $user->id)
            ->whereBetween('created_at', [$window['start'], $window['end']])
            ->whereHas('feature.properties', fn ($query) => $query->whereIn('karbari', $karbaris))
            ->get(['id', 'created_at']);

        foreach ($window['buckets'] as $bucket) {
            $labels[] = $bucket['label'];
            $bought[] = $boughtTrades->filter(
                fn (Trade $trade) => Carbon::parse($trade->getRawOriginal('created_at'))->between($bucket['start'], $bucket['end'])
            )->count();
            $sold[] = $soldTrades->filter(
                fn (Trade $trade) => Carbon::parse($trade->getRawOriginal('created_at'))->between($bucket['start'], $bucket['end'])
            )->count();
        }

        return [
            'data' => [
                'labels' => $labels,
                'bought' => $bought,
                'sold' => $sold,
            ],
        ];
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     * @return array{features: LengthAwarePaginator, map_markers: array<int, array<string, mixed>>}
     */
    public function getFeatures(User $user, ?array $requestedKarbaris = null, ?string $search = null, int $perPage = 15): array
    {
        $karbaris = $this->getEffectiveKarbaris($user, $requestedKarbaris);

        $listQuery = $this->baseFeaturesQuery($user, $karbaris);

        if ($search !== null && $search !== '') {
            $listQuery = $this->applySearch($listQuery, $search);
        }

        $features = $listQuery
            ->with(['properties', 'owner:id,code', 'images'])
            ->paginate($perPage);

        $centers = Feature::batchComputedCenters(
            $features->getCollection()->pluck('id')->all()
        );

        $features->getCollection()->each(function (Feature $feature) use ($centers) {
            $feature->setAttribute('computed_center', $centers->get($feature->id));
        });

        $mapFeatures = $this->baseFeaturesQuery($user, $karbaris)
            ->with('properties:id,feature_id,karbari')
            ->get(['id']);

        $mapCenters = Feature::batchComputedCenters($mapFeatures->pluck('id')->all());

        $mapMarkers = $mapFeatures->map(fn (Feature $feature) => [
            'id' => $feature->id,
            'center' => $mapCenters->get($feature->id),
            'karbari' => $feature->properties->karbari,
        ])->values()->all();

        return [
            'features' => $features,
            'map_markers' => $mapMarkers,
        ];
    }

    /**
     * @param  array<string>  $karbaris
     */
    private function baseFeaturesQuery(User $user, array $karbaris): Builder
    {
        if ($karbaris === []) {
            return Feature::query()->whereRaw('0 = 1');
        }

        return Feature::query()
            ->where('owner_id', $user->id)
            ->whereHas('properties', fn ($query) => $query->whereIn('karbari', $karbaris));
    }

    private function applySearch(Builder $query, string $search): Builder
    {
        return $query->whereHas('properties', function ($query) use ($search) {
            $query->where('id', 'like', '%'.$search.'%')
                ->orWhere('address', 'like', '%'.$search.'%');
        });
    }

    private function getKarbariLabel(string $karbari): string
    {
        return match ($karbari) {
            FeatureIndicators::Amozeshi => 'آموزشی',
            FeatureIndicators::Tejari => 'تجاری',
            FeatureIndicators::Maskoni => 'مسکونی',
            FeatureIndicators::Edari => 'اداری',
            FeatureIndicators::Behdashti => 'بهداشتی',
            FeatureIndicators::FazaSabz => 'فضای سبز',
            FeatureIndicators::Farhangi => 'فرهنگی',
            FeatureIndicators::Parking => 'پارکینگ',
            FeatureIndicators::Mazhabi => 'مذهبی',
            FeatureIndicators::Nemayeshgah => 'نمایشگاه',
            FeatureIndicators::Gardeshgari => 'گردشگری',
            default => 'نامشخص',
        };
    }
}
