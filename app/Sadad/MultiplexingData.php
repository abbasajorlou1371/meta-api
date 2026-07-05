<?php

namespace App\Sadad;

class MultiplexingData
{
    /**
     * Route the full payment amount to a single IBAN (Amount-based multiplexing).
     */
    public static function forAmount(string $iban, int $amount): array
    {
        return [
            'Type' => 'Amount',
            'MultiplexingRows' => [
                [
                    'IbanNumber' => $iban,
                    'Value' => $amount,
                ],
            ],
        ];
    }

    /**
     * Resolve the target IBAN based on asset type.
     *
     * Non-IRR assets route to the main account; IRR routes to the loan account.
     */
    public static function forAsset(string $asset, int $amount): array
    {
        $iban = $asset === 'irr'
            ? config('sadad.loan_iban')
            : config('sadad.main_iban');

        return self::forAmount($iban, $amount);
    }
}
