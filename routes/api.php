<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\PaypalWebhookController;
use App\Exceptions\CustomTestException;
use App\Exceptions\AnotherCustomException;
// Them Redis
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

Route::post('/webhook/stripe', [StripeWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:600,1')
    ->name('stripe.webhook');
Route::post('/webhook/paypal', [PaypalWebhookController::class, 'handleWebhook'])
    ->middleware('throttle:600,1')
    ->name('paypal.webhook');

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/test', fn() => throw new CustomTestException('Lỗi test'));
Route::get('/test2', fn() => throw new AnotherCustomException('Lỗi test 2'));
Route::get('/big-json', function () {
    return response()->json([
        'data' => array_fill(0, 1000, str_repeat('Laravel Octane là siêu nhanh! 🚀', 10)),
    ]);
})->middleware('brotli.compress');

Route::post('/reverb-webhook', function (Request $request) {
    $event = $request->input('event');
    $payload = $request->input('payload');

    Log::info("Reverb Webhook", compact('event', 'payload'));

    return response()->json(['status' => 'success', 'event' => $event], 200);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/presence/here', function (Request $request) {
        $channel = $request->input('channel');
        $users = $request->input('users', []);

        $key = "presence:{$channel}:members";
        Redis::del($key);

        foreach ($users as $user) {
            Redis::hset($key, (string) $user['id'], json_encode([
                'id' => $user['id'],
                'name' => $user['name'],
                'joined_at' => now()->toISOString(),
            ]));
        }

        Redis::expire($key, 600);

        return response()->json(['status' => 'ok']);
    });

    Route::post('/presence/join', function (Request $request) {
        $channel = $request->input('channel');
        $user = $request->input('user');

        $key = "presence:{$channel}:members";

        Redis::hset($key, (string) $user['id'], json_encode([
            'id' => $user['id'],
            'name' => $user['name'],
            'joined_at' => now()->toISOString(),
        ]));

        Redis::expire($key, 600);

        return response()->json(['status' => 'ok']);
    });

    Route::post('/presence/leave', function (Request $request) {
        $channel = $request->input('channel');
        $user = $request->input('user');

        $key = "presence:{$channel}:members";

        Redis::hdel($key, (string) $user['id']);

        return response()->json(['status' => 'ok']);
    });

    Route::get('/presence/{channel}', function ($channel) {
        $key = "presence:{$channel}:members";
        $members = Redis::hgetall($key);

        $users = collect($members)->map(function ($item) {
            return json_decode($item, true);
        })->values();

        return response()->json(['users' => $users]);
    });

});
