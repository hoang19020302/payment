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

Route::post('/unban/{id}', function ($id) {
    $user = User::findOrFail($id);

    // ❌ Xoá flag trong Redis
    Redis::del('banned:user:' . $id);

    // Set flag trong Redis
// Redis::set('banned:user:' . $id, 1);

// // (Optional) Cập nhật DB
// User::where('id', $id)->update(['is_banned' => true]);

// broadcast(new ForceDisconnect($id));
//Redis::setex('banned:user:' . $id, 3600, 1); // Ban 1 giờ

    // ✅ Update DB 
    $user->is_banned = false;
    $user->save();

    // 🔥 Thông báo cho client
    broadcast(new Unbanned($id));

    return response()->json(['message' => 'User unbanned']);
});

Route::get('/swoole-config', function () {
    return response()->json(config('octane.swoole.options'));
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'Ok',
        'hostname' => gethostname(),
    ], 200);
});
