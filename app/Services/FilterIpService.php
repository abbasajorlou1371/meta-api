<?php

namespace App\Services;

use Closure;
use Illuminate\Http\Request;
use App\Models\Ip;

class FilterIpService
{
    public function handle(Request $request, Closure $next)
    {
        return !Ip::where('type', 'api')->where('from', ip2long(request()->ip()))->doesntExist();
    }
}
