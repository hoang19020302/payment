<?php

namespace App\Exceptions;

use Illuminate\Foundation\Configuration\Exceptions;
use App\Exceptions\CustomTestException;
use App\Exceptions\AnotherCustomException;
use App\Exceptions\Handlers\CustomTestExceptionHandler;
use App\Exceptions\Handlers\AnotherCustomExceptionHandler;

class ExceptionHandlerRegistry
{
    public function __invoke(Exceptions $exceptions)
    {
        $exceptions->renderable(function (CustomTestException $e, $request) {
            return (new CustomTestExceptionHandler())->handle($e, $request);
        });

        $exceptions->renderable(function (AnotherCustomException $e, $request) {
            return (new AnotherCustomExceptionHandler())->handle($e, $request);
        });

        // Bạn có thể thêm các exception handler khác ở đây
    }
}
