<?php

namespace App\Exceptions\Handlers;

use App\Exceptions\CustomTestException;
use Illuminate\Http\Request;

class CustomTestExceptionHandler
{
    public function handle(CustomTestException $exception, Request $request)
    {
        return response()->json([
            'error' => 'CustomTestException',
            'message' => $exception->getMessage(),
        ], 400);
    }
}
