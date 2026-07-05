<?php

namespace App\Sadad;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpClient
{
    public static function post(string $url, array $payload): Response
    {
        self::assertAllowedUrl($url);

        return Http::timeout((int) config('sadad.http_timeout', 30))
            ->withHeaders(['User-Agent' => ''])
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);
    }

    private static function assertAllowedUrl(string $url): void
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        $host = parse_url($url, PHP_URL_HOST);

        if ($scheme !== 'https' || $host === null) {
            throw new \InvalidArgumentException('Sadad API must use a valid HTTPS URL.');
        }

        $allowedHosts = config('sadad.allowed_hosts', ['sadad.shaparak.ir']);

        if (! in_array($host, $allowedHosts, true)) {
            Log::warning('Blocked Sadad request to disallowed host.', ['host' => $host]);
            throw new \InvalidArgumentException('Sadad API host is not allowed.');
        }
    }
}
