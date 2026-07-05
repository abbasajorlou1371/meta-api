<?php

return [
    'merchant_id' => env('SADAD_MERCHANT_ID'),
    'terminal_id' => env('SADAD_TERMINAL_ID'),
    'terminal_key' => env('SADAD_TERMINAL_KEY'),
    'main_iban' => env('SADAD_MAIN_IBAN'),
    'loan_iban' => env('SADAD_LOAN_IBAN'),
    'callback_port' => (int) env('SADAD_CALLBACK_PORT', 8080),
    'callback_url' => env('SADAD_CALLBACK_URL'),
    'payment_request_url' => env('SADAD_PAYMENT_REQUEST_URL', 'https://sadad.shaparak.ir/api/v0/Request/PaymentRequest'),
    'verify_url' => env('SADAD_VERIFY_URL', 'https://sadad.shaparak.ir/api/v0/Advice/Verify'),
    'purchase_url' => env('SADAD_PURCHASE_URL', 'https://sadad.shaparak.ir/Purchase'),
    'frontend_redirect_url' => env('SADAD_FRONTEND_REDIRECT_URL', 'https://rgb.irpsc.com/metaverse/payment/verify'),
    'http_timeout' => env('SADAD_HTTP_TIMEOUT', 30),
    'allowed_hosts' => ['sadad.shaparak.ir'],
];
