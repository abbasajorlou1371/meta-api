<?php

namespace App\Sadad;

class Crypto
{
    /**
     * Encrypt data using TripleDES (ECB, PKCS7) with the terminal key.
     */
    public static function encrypt(string $data, ?string $terminalKey = null): string
    {
        $key = self::prepareKey($terminalKey ?? config('sadad.terminal_key'));

        $encrypted = openssl_encrypt(
            $data,
            'DES-EDE3',
            $key,
            OPENSSL_RAW_DATA
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Failed to encrypt Sadad sign data.');
        }

        return base64_encode($encrypted);
    }

    /**
     * Build SignData for PaymentRequest: TerminalId;OrderId;Amount
     */
    public static function signPaymentRequest(string $terminalId, int|string $orderId, int $amount): string
    {
        return self::encrypt("{$terminalId};{$orderId};{$amount}");
    }

    /**
     * Build SignData for Verify: Token
     */
    public static function signVerify(string $token): string
    {
        return self::encrypt($token);
    }

    private static function prepareKey(string $terminalKey): string
    {
        $key = base64_decode($terminalKey, true);

        if ($key === false) {
            throw new \RuntimeException('Invalid Sadad terminal key encoding.');
        }

        if (strlen($key) === 16) {
            $key .= substr($key, 0, 8);
        }

        return $key;
    }
}
