<?php

namespace App\Helpers;

use App\Models\Feature;
use App\Models\User;

class AssetHelper
{

    public static function getAssetTitle(string $asset) : string
    {
        return match ($asset) {
            'psc'    => 'psc',
            'irr'    => 'ریال',
            'yellow' => 'رنگ زرد',
            'blue'   => 'رنگ آبی',
            'red'    => 'رنگ قرمز'
        };
    }

    public static function checkColorBalance(User $user, Feature $feature)
    {
        return match($feature->properties->karbari) {
            FeatureIndicators::Tejari   => $user->assets->red < $feature->properties->stability,
            FeatureIndicators::Maskoni  => $user->assets->yellow < $feature->properties->stability,
            FeatureIndicators::Amozeshi => $user->assets->blue < $feature->properties->stability
        };
    }

    public static function getAssetColor(Feature $feature)
    {
        return match($feature->properties->karbari) {
            FeatureIndicators::Amozeshi => 'blue',
            FeatureIndicators::Tejari   => 'red',
            FeatureIndicators::Maskoni  => 'yellow'
        };
    }
}
