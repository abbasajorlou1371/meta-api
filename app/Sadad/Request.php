<?php

namespace App\Sadad;

use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Log;

class Request
{
    private string $callbackUrl;

    private ?string $additionalData = null;

    private ?array $multiplexingData = null;

    public function __construct(
        private readonly string $merchantId,
        private readonly string $terminalId,
        private readonly int|string $orderId,
        private readonly int $amount,
    ) {
    }

    public function callbackUrl(string $callbackUrl): self
    {
        $this->callbackUrl = $callbackUrl;

        return $this;
    }

    public function additionalData(string $additionalData): self
    {
        $this->additionalData = $additionalData;

        return $this;
    }

    public function multiplexingData(array $multiplexingData): self
    {
        $this->multiplexingData = $multiplexingData;

        return $this;
    }

    public function multiplexingForAsset(string $asset): self
    {
        $this->multiplexingData = MultiplexingData::forAsset($asset, $this->amount);

        return $this;
    }

    public function send(): RequestResponse
    {
        $iranTime = new DateTime('now', new DateTimeZone('Asia/Tehran'));

        $payload = [
            'MerchantId' => $this->merchantId,
            'TerminalId' => $this->terminalId,
            'Amount' => $this->amount,
            'OrderId' => (int) $this->orderId,
            'LocalDateTime' => $iranTime->format('m/d/Y g:i:s a'),
            'ReturnUrl' => $this->callbackUrl,
            'SignData' => Crypto::signPaymentRequest($this->terminalId, $this->orderId, $this->amount),
        ];

        if ($this->additionalData !== null) {
            $payload['AdditionalData'] = $this->additionalData;
        }

        if ($this->multiplexingData !== null) {
            $payload['MultiplexingData'] = $this->multiplexingData;
        }

        Log::info('Sadad payment request', [
            'payload' => $payload,
        ]);

        $response = HttpClient::post(config('sadad.payment_request_url'), $payload);

        if (! $response->successful()) {
            Log::error('Sadad payment request failed.', ['http_status' => $response->status()]);
            throw new \RuntimeException('Payment gateway request failed.');
        }

        $result = (object) $response->json();
        $requestResponse = new RequestResponse($result);

        if (! $requestResponse->success()) {
            Log::warning('Sadad PaymentRequest rejected.', [
                'res_code' => $requestResponse->resCode(),
                'description' => $requestResponse->message(),
            ]);
        }

        return $requestResponse;
    }
}
