<?php

namespace App\Sadad;

use Illuminate\Support\Facades\Log;

class Verification
{
    public function __construct(private readonly string $token)
    {
    }

    public function send(): VerificationResponse
    {
        $payload = [
            'Token' => $this->token,
            'SignData' => Crypto::signVerify($this->token),
        ];

        $response = HttpClient::post(config('sadad.verify_url'), $payload);

        if (! $response->successful()) {
            Log::error('Sadad verify request failed.', ['http_status' => $response->status()]);
            throw new \RuntimeException('Payment gateway verification failed.');
        }

        return new VerificationResponse((object) $response->json());
    }
}
