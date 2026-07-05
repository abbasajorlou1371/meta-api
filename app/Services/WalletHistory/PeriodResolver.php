<?php

namespace App\Services\WalletHistory;

use Carbon\Carbon;
use InvalidArgumentException;
use Morilog\Jalali\Jalalian;

class PeriodResolver
{
    public const PERIODS = [
        'daily',
        'weekly',
        'monthly',
        'yearly',
    ];

    /**
     * Resolve the active period window and chart buckets.
     *
     * @return array{
     *     period: string,
     *     start: Carbon,
     *     end: Carbon,
     *     granularity: string,
     *     buckets: array<int, array{start: Carbon, end: Carbon, label: string}>
     * }
     */
    public function resolve(string $period, ?Carbon $reference = null): array
    {
        $this->assertValidPeriod($period);

        $end = ($reference ?? now())->copy()->endOfSecond();
        $start = match ($period) {
            'daily' => $end->copy()->subHours(24)->startOfSecond(),
            'weekly' => $end->copy()->subDays(6)->startOfDay(),
            'monthly' => $end->copy()->subDays(29)->startOfDay(),
            'yearly' => $end->copy()->subMonths(11)->startOfMonth(),
        };

        return [
            'period' => $period,
            'start' => $start,
            'end' => $end,
            'granularity' => $this->granularityFor($period),
            'buckets' => $this->buildBuckets($period, $start, $end),
        ];
    }

    /**
     * Resolve the immediately preceding period of equal length.
     *
     * @return array{start: Carbon, end: Carbon}
     */
    public function resolvePrevious(string $period, ?Carbon $reference = null): array
    {
        $current = $this->resolve($period, $reference);
        $duration = $current['start']->diffInSeconds($current['end']);

        return [
            'start' => $current['start']->copy()->subSeconds($duration + 1),
            'end' => $current['start']->copy()->subSecond(),
        ];
    }

    private function granularityFor(string $period): string
    {
        return match ($period) {
            'daily' => 'hourly',
            'weekly' => 'daily',
            'monthly' => 'weekly',
            'yearly' => 'monthly',
        };
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon, label: string}>
     */
    private function buildBuckets(string $period, Carbon $start, Carbon $end): array
    {
        return match ($period) {
            'daily' => $this->hourlyBuckets($end),
            'weekly' => $this->dailyBuckets($end, 7),
            'monthly' => $this->weeklyBuckets($start, $end),
            'yearly' => $this->monthlyBuckets($end, 12),
        };
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon, label: string}>
     */
    private function hourlyBuckets(Carbon $end): array
    {
        return collect(range(23, 0))
            ->map(function (int $offset) use ($end) {
                $bucketEnd = $end->copy()->subHours($offset)->endOfHour();
                $bucketStart = $bucketEnd->copy()->startOfHour();

                return [
                    'start' => $bucketStart,
                    'end' => $bucketEnd,
                    'label' => Jalalian::fromCarbon($bucketStart)->format('H:i'),
                ];
            })
            ->reverse()
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon, label: string}>
     */
    private function dailyBuckets(Carbon $end, int $days): array
    {
        return collect(range($days - 1, 0))
            ->map(function (int $offset) use ($end) {
                $bucketDate = $end->copy()->subDays($offset);
                $bucketStart = $bucketDate->copy()->startOfDay();
                $bucketEnd = $bucketDate->copy()->endOfDay();

                return [
                    'start' => $bucketStart,
                    'end' => $bucketEnd,
                    'label' => Jalalian::fromCarbon($bucketStart)->format('Y/m/d'),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon, label: string}>
     */
    private function weeklyBuckets(Carbon $start, Carbon $end): array
    {
        $buckets = [];
        $cursor = $start->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $bucketStart = $cursor->copy();
            $bucketEnd = $cursor->copy()->addDays(6)->endOfDay();

            if ($bucketEnd->gt($end)) {
                $bucketEnd = $end->copy();
            }

            $buckets[] = [
                'start' => $bucketStart,
                'end' => $bucketEnd,
                'label' => Jalalian::fromCarbon($bucketStart)->format('Y/m/d'),
            ];

            $cursor->addDays(7);
        }

        return $buckets;
    }

    /**
     * @return array<int, array{start: Carbon, end: Carbon, label: string}>
     */
    private function monthlyBuckets(Carbon $end, int $months): array
    {
        return collect(range($months - 1, 0))
            ->map(function (int $offset) use ($end) {
                $bucketDate = $end->copy()->subMonths($offset);
                $bucketStart = $bucketDate->copy()->startOfMonth();
                $bucketEnd = $bucketDate->copy()->endOfMonth();

                return [
                    'start' => $bucketStart,
                    'end' => $bucketEnd,
                    'label' => Jalalian::fromCarbon($bucketStart)->format('%B %Y'),
                ];
            })
            ->values()
            ->all();
    }

    private function assertValidPeriod(string $period): void
    {
        if (! in_array($period, self::PERIODS, true)) {
            throw new InvalidArgumentException("Invalid period [{$period}] provided.");
        }
    }
}
