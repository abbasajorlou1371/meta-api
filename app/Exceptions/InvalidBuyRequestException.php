<?php

namespace App\Exceptions;

use App\Models\BuyFeatureRequest;
use Exception;

class InvalidBuyRequestException extends Exception
{
    public function render(BuyFeatureRequest $buyFeatureRequest) {
        return $buyFeatureRequest->status === -1 ||
               $buyFeatureRequest->status === 1;
    }
}
