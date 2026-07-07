<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureTradeHistoryItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource['id'],
            'type' => $this->resource['type'],
            'participant_code' => $this->resource['participant_code'],
            'participant_label' => $this->resource['participant_label'],
            'date_time' => $this->resource['date_time'],
            'price' => $this->resource['price'],
        ];
    }
}
