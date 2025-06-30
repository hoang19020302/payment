<?php

namespace App\Exceptions\Handlers;

use App\Exceptions\AnotherCustomException;
use Illuminate\Http\Request;

class AnotherCustomExceptionHandler
{
    public function handle(AnotherCustomException $exception, Request $request)
    {
        return response()->json([
            'error' => 'AnotherCustomException',
            'message' => $exception->getMessage(),
        ], 422);
    }
}
