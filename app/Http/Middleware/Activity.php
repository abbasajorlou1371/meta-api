<?php

namespace App\Http\Middleware;

use App\Events\UserStatusChanged;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class Activity
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
        if (auth()->user()) {
            $latestActivity = $request->user()->latestActivity;
            $start = Carbon::parse($latestActivity->start);
            if (is_null($latestActivity->end)) {
                if ($start->diffInMinutes(now()) > 5) {
                    $latestActivity->update([
                        'end' => now()->subMinutes($start->diffInMinutes(now()) - 5),
                        'total' => 5,
                        'ip' => $request->ip(),
                    ]);
                    $request->user()->activities()->create([
                        'start' => now()
                    ]);
                }
            }
            $request->user()->update(['last_seen' => now()]);
            broadcast(new UserStatusChanged([
                'code' => $request->user()->code,
                'status' => 'online',
            ]));
        }
        return $next($request);
    }
}
