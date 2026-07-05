<?php

use App\Sadad\Sadad;

if (! function_exists('sadad')) {
    function sadad(): Sadad
    {
        return new Sadad();
    }
}

if (! function_exists('sadad_callback_url')) {
    /**
     * Build the Sadad ReturnUrl with the configured callback port (default 8080).
     */
    function sadad_callback_url(): string
    {
        if ($url = config('sadad.callback_url')) {
            return $url;
        }

        $parts = parse_url(rtrim(config('app.url'), '/'));
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? ($parts['path'] ?? 'localhost');
        $port = (int) config('sadad.callback_port', 8080);

        return sprintf('%s://%s:%d/api/order/callback', $scheme, $host, $port);
    }
}
