<?php

namespace App\Http\Requests\WalletHistory;

use App\Services\WalletHistory\PeriodResolver;
use App\Services\WalletHistory\WalletAsset;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WalletHistoryChartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Query parameters for the public wallet asset history chart endpoint.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'period' => ['required', Rule::in(PeriodResolver::PERIODS)],
            'assets' => ['sometimes', 'array'],
            'assets.*' => ['string', Rule::in(WalletAsset::ALL)],
        ];
    }
}
