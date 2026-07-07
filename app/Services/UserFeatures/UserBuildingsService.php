<?php

namespace App\Services\UserFeatures;

use App\Helpers\FeatureIndicators;
use App\Models\Feature\Building;
use App\Models\User;
use App\Services\WalletHistory\PeriodResolver;
use Carbon\Carbon;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class UserBuildingsService
{
    public function __construct(
        private readonly UserFeaturesService $userFeaturesService,
        private readonly PeriodResolver $periodResolver,
    ) {
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     * @return array{data: array<int, array<string, mixed>>}
     */
    public function getSummary(User $user, ?array $requestedKarbaris = null): array
    {
        $karbaris = $this->userFeaturesService->getEffectiveKarbaris($user, $requestedKarbaris);

        if ($karbaris === []) {
            return ['data' => []];
        }

        $counts = Building::query()
            ->constructionCompleted()
            ->join('features', 'features.id', '=', 'buildings.feature_id')
            ->join('feature_properties', 'feature_properties.feature_id', '=', 'buildings.feature_id')
            ->where('features.owner_id', $user->id)
            ->whereIn('feature_properties.karbari', $karbaris)
            ->selectRaw('feature_properties.karbari, COUNT(buildings.id) as count')
            ->groupBy('feature_properties.karbari')
            ->pluck('count', 'karbari');

        $data = collect($karbaris)->map(function (string $karbari) use ($counts) {
            return [
                'karbari' => $karbari,
                'label' => $this->getKarbariLabel($karbari),
                'count' => (int) ($counts[$karbari] ?? 0),
            ];
        })->values()->all();

        return ['data' => $data];
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     * @return array{data: array{labels: array<int, string>, completed: array<int, int>}, period: string}
     */
    public function getChart(User $user, string $period, ?array $requestedKarbaris = null): array
    {
        $karbaris = $this->userFeaturesService->getEffectiveKarbaris($user, $requestedKarbaris);
        $window = $this->periodResolver->resolve($period);

        $labels = [];
        $completed = [];

        if ($karbaris === []) {
            foreach ($window['buckets'] as $bucket) {
                $labels[] = $bucket['label'];
                $completed[] = 0;
            }

            return [
                'data' => [
                    'labels' => $labels,
                    'completed' => $completed,
                ],
                'period' => $period,
            ];
        }

        $buildings = Building::query()
            ->constructionCompleted()
            ->whereBetween('construction_end_date', [$window['start'], $window['end']])
            ->whereHas('feature', fn (Builder $query) => $query->where('owner_id', $user->id))
            ->whereHas('feature.properties', fn (Builder $query) => $query->whereIn('karbari', $karbaris))
            ->get(['id', 'construction_end_date']);

        foreach ($window['buckets'] as $bucket) {
            $labels[] = $bucket['label'];
            $completed[] = $buildings->filter(
                fn (Building $building) => Carbon::parse($building->getRawOriginal('construction_end_date'))
                    ->between($bucket['start'], $bucket['end'])
            )->count();
        }

        return [
            'data' => [
                'labels' => $labels,
                'completed' => $completed,
            ],
            'period' => $period,
        ];
    }

    /**
     * @param  array<string>|null  $requestedKarbaris
     */
    public function getBuildings(User $user, ?array $requestedKarbaris = null, int $perPage = 10): LengthAwarePaginator
    {
        $karbaris = $this->userFeaturesService->getEffectiveKarbaris($user, $requestedKarbaris);

        if ($karbaris === []) {
            return Building::query()->whereRaw('0 = 1')->paginate($perPage);
        }

        return Building::query()
            ->constructionCompleted()
            ->whereHas('feature', fn (Builder $query) => $query->where('owner_id', $user->id))
            ->whereHas('feature.properties', fn (Builder $query) => $query->whereIn('karbari', $karbaris))
            ->with([
                'feature.properties:id,feature_id,karbari',
                'buildingModel:id,attributes',
            ])
            ->orderByDesc('construction_end_date')
            ->paginate($perPage);
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
