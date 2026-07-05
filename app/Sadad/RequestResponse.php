<?php

namespace App\Sadad;

class RequestResponse
{
    private int $resCode;

    private ?string $token;

    private ?string $description;

    public function __construct(object $result)
    {
        $this->resCode = (int) ($result->ResCode ?? -1);
        $this->token = isset($result->Token) && $result->Token !== ''
            ? (string) $result->Token
            : null;
        $this->description = isset($result->Description) ? (string) $result->Description : null;
    }

    public function resCode(): int
    {
        return $this->resCode;
    }

    public function success(): bool
    {
        return $this->resCode === 0 && ! empty($this->token);
    }

    public function message(): ?string
    {
        return $this->description;
    }

    public function token(): ?string
    {
        return $this->token;
    }

    public function url(): string
    {
        if (! $this->success()) {
            return '';
        }

        return config('sadad.purchase_url') . '?Token=' . urlencode($this->token);
    }

    public function error(): Error
    {
        return new Error($this->resCode, $this->description);
    }
}
