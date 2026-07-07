<?php

namespace App\Services\Feature;

use App\Models\Feature;
use App\Models\Trade;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Morilog\Jalali\Jalalian;

class FeatureTradeHistoryService
{
    public const SYSTEM_USER_CODE = 'hm-2000000';

    public const SYSTEM_OWNER_LABEL = 'متارنگ سیستم';

    public const PER_PAGE = 10;

    private const COLOR_ASSETS = ['blue', 'red', 'yellow'];

    public function paginate(Feature $feature, int $page = 1): LengthAwarePaginator
    {
        $systemUser = User::query()->firstWhere('code', self::SYSTEM_USER_CODE);

        $items = $this->buildHistoryItems($feature, $systemUser);

        $total = $items->count();
        $offset = max(0, ($page - 1) * self::PER_PAGE);

        return new LengthAwarePaginator(
            $items->slice($offset, self::PER_PAGE)->values(),
            $total,
            self::PER_PAGE,
            $page,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ],
        );
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function buildHistoryItems(Feature $feature, ?User $systemUser = null): Collection
    {
        $systemUser ??= User::query()->firstWhere('code', self::SYSTEM_USER_CODE);

        $feature->loadMissing('properties');

        $trades = Trade::query()
            ->where('feature_id', $feature->id)
            ->with([
                'buyer:id,code,name',
                'seller:id,code,name',
                'transactions:id,payable_id,payable_type,asset,amount,action',
            ])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        $items = $trades->map(
            fn (Trade $trade) => $this->transformTrade($trade, $systemUser),
        );

        $genesis = $this->buildGenesisEntry($feature);

        if ($genesis !== null) {
            $items->push($genesis);
        }

        return $items->values();
    }

    /**
     * @return array<string, mixed>
     */
    private function transformTrade(Trade $trade, ?User $systemUser): array
    {
        $buyer = $trade->buyer;
        $timestamp = $this->resolveTradeTimestamp($trade);

        return [
            'id' => $trade->id,
            'type' => 'trade',
            'participant_code' => $buyer ? strtoupper($buyer->code) : null,
            'participant_label' => $buyer?->name,
            'date_time' => $this->formatDateTime($timestamp),
            'price' => $this->resolvePrice($trade, $systemUser),
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function buildGenesisEntry(Feature $feature): ?array
    {
        $timestamp = $feature->getRawOriginal('created_at') ?? now();

        return [
            'id' => null,
            'type' => 'genesis',
            'participant_code' => null,
            'participant_label' => self::SYSTEM_OWNER_LABEL,
            'date_time' => $this->formatDateTime(Carbon::parse($timestamp)),
            'price' => [
                'type' => 'currency',
                'price_psc' => 0,
                'price_irr' => 0,
                'color' => null,
                'color_name' => null,
                'color_amount' => null,
            ],
        ];
    }

    private function resolveTradeTimestamp(Trade $trade): Carbon
    {
        $createdAt = $trade->getRawOriginal('created_at');

        if ($createdAt !== null) {
            return Carbon::parse($createdAt);
        }

        if ($trade->date !== null) {
            return Carbon::parse($trade->date)->startOfDay();
        }

        return now();
    }

    /**
     * @return array<string, mixed>
     */
    private function formatDateTime(Carbon $timestamp): array
    {
        $jalali = Jalalian::fromCarbon($timestamp);

        return [
            'date' => $jalali->format('Y/m/d'),
            'month_name' => $jalali->format('%B'),
            'year' => $jalali->getYear(),
            'time' => $jalali->format('H:i:s'),
            'formatted' => sprintf(
                '%s %s | %s',
                $jalali->format('%B'),
                $jalali->getYear(),
                $jalali->format('H:i:s'),
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function resolvePrice(Trade $trade, ?User $systemUser): array
    {
        if ($this->isSystemPurchase($trade, $systemUser)) {
            $colorTransaction = $trade->transactions
                ->first(fn ($transaction) => $transaction->action === 'withdraw'
                    && in_array($transaction->asset, self::COLOR_ASSETS, true));

            $color = $colorTransaction?->asset;
            $amount = $colorTransaction?->amount ?? 0;

            return [
                'type' => 'color',
                'price_psc' => null,
                'price_irr' => null,
                'color' => $color,
                'color_name' => $this->colorName($color),
                'color_amount' => $amount,
            ];
        }

        return [
            'type' => 'currency',
            'price_psc' => (int) ($trade->psc_amount ?? 0),
            'price_irr' => (int) ($trade->irr_amount ?? 0),
            'color' => null,
            'color_name' => null,
            'color_amount' => null,
        ];
    }

    private function isSystemPurchase(Trade $trade, ?User $systemUser): bool
    {
        if ($systemUser !== null && $trade->seller_id === $systemUser->id) {
            return true;
        }

        return ($trade->psc_amount ?? 0) == 0
            && ($trade->irr_amount ?? 0) == 0
            && $trade->transactions->contains(
                fn ($transaction) => $transaction->action === 'withdraw'
                    && in_array($transaction->asset, self::COLOR_ASSETS, true),
            );
    }

    private function colorName(?string $color): ?string
    {
        return match ($color) {
            'blue' => 'آبی',
            'red' => 'قرمز',
            'yellow' => 'زرد',
            default => null,
        };
    }
}
