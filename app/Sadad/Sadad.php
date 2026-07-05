<?php

namespace App\Sadad;

class Sadad
{
    private int|string|null $orderId = null;

    private ?int $amount = null;

    private ?string $asset = null;

    private ?string $token = null;

    public function orderId(int|string $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function amount(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function asset(string $asset): self
    {
        $this->asset = $asset;

        return $this;
    }

    public function token(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function request(): Request
    {
        $request = new Request(
            config('sadad.merchant_id'),
            config('sadad.terminal_id'),
            $this->orderId,
            $this->amount,
        );

        if ($this->asset !== null) {
            $request->multiplexingForAsset($this->asset);
        }

        return $request;
    }

    public function verification(): Verification
    {
        return new Verification($this->token);
    }
}
