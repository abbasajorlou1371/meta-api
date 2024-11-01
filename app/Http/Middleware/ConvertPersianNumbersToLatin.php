<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConvertPersianNumbersToLatin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $request->merge(array_map(function ($value) {
            return is_string($value) ? $this->convertPersianNumbersToLatin($value) : $value;
        }, $request->all()));

        return $next($request);
    }

    /**
     * Convert Persian numbers to Latin
     *
     * @param string $string
     * @return string
     */
    private function convertPersianNumbersToLatin(string $string): string
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $latinNumbers = range(0, 9);

        return str_replace($persianNumbers, $latinNumbers, $string);
    }
}
