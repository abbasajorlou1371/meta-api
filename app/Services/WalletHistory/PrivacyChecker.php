<?php

namespace App\Services\WalletHistory;

use App\Models\Setting;
use App\Models\User;

class PrivacyChecker
{
    /**
     * @var array<string, string|null>
     */
    public const PRIVACY_KEYS = [
        'psc' => 'psc_transactions',
        'irr' => 'irr_transactions',
        'red' => 'red_transactions',
        'blue' => 'blue_transactions',
        'yellow' => 'yellow_transactions',
        'satisfaction' => 'satisfaction',
        'effect' => null,
    ];

    public function isVisible(User $user, string $asset): bool
    {
        $privacyKey = self::PRIVACY_KEYS[$asset] ?? null;

        if ($privacyKey === null) {
            return true;
        }

        $privacy = $this->privacySettings($user);

        return (bool) ($privacy[$privacyKey] ?? 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function privacySettings(User $user): array
    {
        $settings = $user->relationLoaded('settings')
            ? $user->settings
            : $user->settings()->first();

        if ($settings?->privacy) {
            return $settings->privacy;
        }

        $defaults = json_decode((new Setting())->getAttributes()['privacy'], true);

        return is_array($defaults) ? $defaults : [];
    }
}
