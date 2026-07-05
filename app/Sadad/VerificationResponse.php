<?php

namespace App\Sadad;

class VerificationResponse
{
    private int $resCode;

    private ?string $retrievalRefNo;

    private ?string $systemTraceNo;

    private ?int $amount;

    private ?int $orderId;

    private ?string $cardHolderFullName;

    public function __construct(object $result)
    {
        $this->resCode = (int) ($result->ResCode ?? -1);
        $this->retrievalRefNo = isset($result->RetrivalRefNo) ? (string) $result->RetrivalRefNo : null;
        $this->systemTraceNo = isset($result->SystemTraceNo) ? (string) $result->SystemTraceNo : null;
        $this->amount = isset($result->Amount) ? (int) $result->Amount : null;
        $this->orderId = isset($result->OrderId) ? (int) $result->OrderId : null;
        $this->cardHolderFullName = $result->CardHolderFullName ?? null;
    }

    public function status(): int
    {
        return $this->resCode;
    }

    public function referenceId(): ?string
    {
        return $this->retrievalRefNo;
    }

    public function systemTraceNo(): ?string
    {
        return $this->systemTraceNo;
    }

    public function amount(): ?int
    {
        return $this->amount;
    }

    public function orderId(): ?int
    {
        return $this->orderId;
    }

    public function success(): bool
    {
        return in_array($this->resCode, [0, 100], true) && ! empty($this->retrievalRefNo);
    }

    public function error(): Error
    {
        return new Error($this->resCode);
    }
}
