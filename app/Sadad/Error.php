<?php

namespace App\Sadad;

class Error
{
    public function __construct(private readonly int $code)
    {
    }

    public function code(): int
    {
        return $this->code;
    }

    public function message(): string
    {
        return match ($this->code) {
            3 => 'پذیرنده کارت فعال نیست',
            23 => 'پذیرنده کارت نامعتبر است',
            58 => 'انجام تراکنش مربوطه توسط پایانه انجام دهنده مجاز نمی باشد',
            61 => 'مبلغ تراکنش از حد مجاز بالاتر است',
            1011 => 'شماره سفارش تکراری می باشد',
            1025 => 'امضا تراکنش نامعتبر است',
            1104 => 'اطلاعات تسهیم صحیح نیست',
            default => 'خطای ناشناخته در درگاه پرداخت',
        };
    }
}
