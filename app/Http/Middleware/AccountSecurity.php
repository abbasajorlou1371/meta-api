<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;

class AccountSecurity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $accountSecurity = $request->user()->accountSecurity;
        if (is_null($accountSecurity) || time() > $accountSecurity?->until) {
            return $request->expectsJson() ?
                abort(403, 'جهت ادامه امنیت حساب کاربری خود را غیر فعال کنید!')
                : RouteServiceProvider::HOME;
        }
        $accountSecurity->update(['last_activity' => time()]);
        return $next($request);
    }
}
