<?php

return [
    'merchant_id' => env('ZARINPAL_MERCHANT_ID'),
    'currency' => env('ZARINPAL_CURRENCY'),
    'curl' => [
        'verify' => 'https://api.zarinpal.com/pg/v4/payment/verify.json',
        'post' => 'https://api.zarinpal.com/pg/v4/payment/request.json'
    ]
];
