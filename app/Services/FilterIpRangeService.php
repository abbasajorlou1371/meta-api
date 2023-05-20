<?php

namespace App\Services;

use App\Models\Ip;
use Closure;
use Illuminate\Http\Request;

class FilterIpRangeService
{
    public function handle(Request $request, Closure $next)
    {
        return  Ip::whereType('range')->where('from', '<=', ip2long(request()->ip()))
            ->where('to', '>=', ip2long(request()->ip()))
            ->doesntExist()
            ? $next($request)
            : true;
    }
}
