<?php

namespace App\Http\Middleware;

use App\Services\MyService;

class ResetServiceState
{
    public function handle($request, \Closure $next)
    {
        MyService::clear(); // Reset dữ liệu trước mỗi request

        return $next($request);
    }
}
