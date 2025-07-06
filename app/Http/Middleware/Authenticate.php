<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        // Nếu là API thì trả về JSON 401
        if ($request->expectsJson()) {
            abort(response()->json([
                'error' => 'Unauthenticated'
            ], 401));
        }

        // Ngược lại thì redirect về route login nếu có
        return route('login');
    }
}

