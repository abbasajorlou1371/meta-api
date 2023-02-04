<?php

namespace App\Policies;

use App\Models\Feature;
use App\Models\User;
use App\Helpers\FeatureIndicators;
use App\Models\BuyFeatureRequest;
use App\Models\Feature\FeatureLimit;
use App\Models\LimitedFeaturePurchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class FeaturePolicy
{
    use HandlesAuthorization;

    private $notAllowedToBeSoldFeatures = [];
    private $soldAndNotPriced = [];
    private $limitedFeatures = [];
    private $sellLimitedFeatures = [];

    public function __construct()
    {
        $this->notAllowedToBeSoldFeatures = [
            FeatureIndicators::Edari,
            FeatureIndicators::Farhangi,
            FeatureIndicators::FazaSabz,
            FeatureIndicators::Parking,
            FeatureIndicators::Gardeshgari,
            FeatureIndicators::Nemayeshgah,
            FeatureIndicators::Behdashti,
        ];

        $this->sellLimitedFeatures = [
            FeatureIndicators::MaskoniNotAllowedToBeSold,
            FeatureIndicators::TejariNotAllowedToBeSold,
            FeatureIndicators::AmoozeshiNotAllowedToBeSold,
        ];

        $this->soldAndNotPriced = [
            FeatureIndicators::MaskoniSoldAndNotPriced,
            FeatureIndicators::TejariSoldAndNotPriced,
            FeatureIndicators::AmozeshiSoldAndNotPriced,
            FeatureIndicators::MaskoniNotPriced,
            FeatureIndicators::TejariNotPriced,
            FeatureIndicators::AmozeshiNotPriced,
        ];

        $this->limitedFeatures = [
            FeatureIndicators::MaskoniTradingLimited,
            FeatureIndicators::TejariTradingLimited,
            FeatureIndicators::AmoozeshiTradingLimited,
        ];
    }

    public function buy(User $user, Feature $feature)
    {
        $notLimited = true;
        $properties = $feature->properties;

        if (in_array($feature->properties->rgb, $this->limitedFeatures)) {

            $featureLimit = FeatureLimit::where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->where('start_id', '<=', $properties->id)
                ->where('end_id', '>=', $properties->id)
                ->first();

            if ($featureLimit->verified_kyc_limit) {
                $notLimited = $user->verified();
            }

            if ($featureLimit->more_than_18_limit) {
                $notLimited = !$user->isUnderEighteen();
            }

            if ($featureLimit->under_18_limit) {
                $notLimited = $user->isUnderEighteen();
            }

            if ($featureLimit->individual_buy_limit) {
                $limitedFeaturesBuyCount = LimitedFeaturePurchase::where('user_id', $user->id)
                ->where('feature_limit_id', $featureLimit->id)
                ->count();
                $notLimited = $limitedFeaturesBuyCount < $featureLimit->individual_buy_limit;
            }
        }

        return !in_array($properties->rgb, $this->sellLimitedFeatures)
            && !in_array($properties->karbari, $this->notAllowedToBeSoldFeatures)
            && !in_array($properties->rgb, $this->soldAndNotPriced)
            && $feature->owner->isNot($user)
            && !$feature->locked()
            && $notLimited;
    }

    public function sell(User $user, Feature $feature)
    {
        $hasUnderEighteenPermissions = true;
        if ($user->isUnderEighteen()) {
            $hasUnderEighteenPermissions = $user->permissions?->verified && $user->permissions?->SF;
        }
        return $feature->owner->is($user)
            && !in_array($feature->properties->rgb, $this->sellLimitedFeatures)
            && $user->verified()
            && !$feature->hasPendingRequests()
            && !$feature->locked()
            && is_null($feature->dynasty)
            && $hasUnderEighteenPermissions;
    }

    public function sendBuyRequest(User $user, Feature $feature)
    {
        $rgb = User::firstWhere('code', 'hm-2000000');
        return BuyFeatureRequest::where('buyer_id', $user->id)
            ->where('feature_id', $feature->id)
            ->where('status', 0)
            ->doesntExist()
            && $feature->owner->isNot($rgb)
            && $feature->owner->isNot($user);
    }
}
