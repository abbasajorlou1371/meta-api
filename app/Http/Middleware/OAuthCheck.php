<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class OAuthCheck
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
        if (Auth::check()) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $request->user()->access_token,
            ])
                ->acceptJson()
                ->get(config('app.oauth_server_url') . '/api/auth/check');
            

            if ($response->status() !== 200) {
                Auth::logout();
                $request->user()->tokens()->delete();
                $request->user()->logedOut();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        return $next($request);
    }
}
