<?php

namespace App\Helpers;

use App\Models\Feature;
use App\Helpers\FeatureIndicators;

class FeatureHelper
{
    public static function getFeatureColor(Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Amozeshi:
                return 'آبی';
                break;
            case FeatureIndicators::Tejari:
                return 'قرمز';
                break;
            case FeatureIndicators::Maskoni:
                return 'زرد';
                break;
        }
    }

    public static function getFeatureName(Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Amozeshi:
                return 'آموزشی';
                break;
            case FeatureIndicators::Tejari:
                return 'تجاری';
                break;
            case FeatureIndicators::Maskoni:
                return 'مسکونی';
                break;
        }
    }

    public static function getSoldAndNotPricedFeatureStatusColor(Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Amozeshi:
                return FeatureIndicators::AmozeshiSoldAndNotPriced;
                break;
            case FeatureIndicators::Tejari:
                return FeatureIndicators::TejariSoldAndNotPriced;
                break;
            case FeatureIndicators::Maskoni:
                return FeatureIndicators::MaskoniSoldAndNotPriced;
                break;
        }
    }

    public static function getSoldAndPricedFeatureStatusColor(Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Amozeshi:
                return FeatureIndicators::AmozeshiSoldAndPriced;
                break;
            case FeatureIndicators::Tejari:
                return FeatureIndicators::TejariSoldAndPriced;
                break;
            case FeatureIndicators::Maskoni:
                return FeatureIndicators::MaskoniSoldAndPriced;
                break;
        }
    }

    public static function changeStatus(Feature $feature)
    {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Maskoni:
                return FeatureIndicators::MaskoniSoldAndPriced;
                break;
            case FeatureIndicators::Tejari:
                return FeatureIndicators::TejariSoldAndPriced;
                break;
            case FeatureIndicators::Amozeshi:
                return FeatureIndicators::AmozeshiSoldAndPriced;
                break;
        }
    }

    public static function cancelSellRequest(Feature $feature) {
        switch ($feature->properties->karbari) {
            case FeatureIndicators::Maskoni:
                return FeatureIndicators::MaskoniSoldAndNotPriced;
                break;
            case FeatureIndicators::Tejari:
                return FeatureIndicators::TejariSoldAndNotPriced;
                break;
            case FeatureIndicators::Amozeshi:
                return FeatureIndicators::AmozeshiSoldAndNotPriced;
                break;
        }

    }

    public static function cancelBuyRequests(Feature $feature)
    {
        if( isset($feature->buyRequests ))
        {
            $requests = $feature->buyRequests;

            foreach($requests as $request)
            {
                $buyer = $request->buyer;
                $lockedAsset = $request->lockedAsset;

                $buyer->assets->increment('psc', $lockedAsset->psc);
                $buyer->assets->increment('irr', $lockedAsset->irr);

                $request->lockedAsset->update([
                    'status' => -1
                ]);
                $request->update([
                    'status' => -1
                ]);
            }
        }
    }

    public static function updateMapFile(Feature $feature, $status) {
        $fileName = $feature->map->name;

        $file_open = file_get_contents(public_path('/map/layers/'.$fileName.'.js'));

        $map = json_decode(explode('=', $file_open)[1], true);

        foreach($map['features'] as $key => $map_feature) {
            if($map['features'][$key]['properties']['id'] === $feature->properties->id) {
                $map['features'][$key]['properties']['rgb'] = $status;
                $map['features'][$key]['properties']['owner'] = $feature->owner->name;
            }
        }

        file_put_contents(public_path('map/layers/' . $fileName . '.js'),
         'var json_' . $fileName . ' = ' . json_encode($map));
    }
}
