<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PaypalWebhookController;
use App\Exceptions\CustomTestException;
use App\Exceptions\AnotherCustomException;

Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:600,1')
    ->name('stripe.webhook');
Route::post('/webhook/paypal', [PaypalWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:600,1')
    ->name('paypal.webhook');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test1', fn() => throw new CustomTestException('Lỗi test 1'));
Route::get('/test2', fn() => throw new AnotherCustomException('Lỗi test 2'));
Route::get('/big-json', function () {
    return response()->json([
        'data' => array_fill(0, 1000, str_repeat('Laravel Octane là siêu nhanh! 🚀', 10)),
    ]);
})->middleware('brotli.compress');
