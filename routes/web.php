<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Charge;

Route::middleware('ngrok.https')->group(function () {
    Route::get('/', function () {
        return view('home');
    })->name('home');

    Route::get('/fake-login-one', function () {
        // Đăng nhập user 1 đầu tiên (tạo user 1 nếu chưa có)
        $user = User::where('name', 'Test User 1')->first() ?? User::factory()->create(['name' => 'Test User 1']);
        Auth::login($user);
        return redirect('/private');
    });

    Route::get('/fake-login-two', function () {
        // Đăng nhập user 2 đầu tiên (tạo user 1 nếu chưa có)
        $user = User::where('name', 'Test User 2')->first() ?? User::factory()->create(['name' => 'Test User 2']);
        Auth::login($user);
        return redirect('/private');
    });

    Route::get('/private', function () {
        return view('private');
    })->middleware('auth');

    Route::get('/broadcast', function () {
        return view('broadcast');
    })->middleware('auth')->name('broadcast');

    Route::post('/broadcast', function (Request $request) {
        $msg = $request->input('msg');
        $message = [
            'content' => 'Hello private from backend: ' . $msg,
            'timestamp' => now(),
        ];
        event(new \App\Events\MessageSent($message, auth()->id()));
        return redirect()->back()->with('success', 'Message sent successfully!');
    })->name('broadcast.send');

    Route::get('/payment', [PaymentController::class, 'showPaymentPage'])->name('payment.page');

    Route::post('/stripe/checkout', [PaymentController::class, 'stripeCheckout'])->name('stripe.checkout');

    Route::post('/paypal/checkout', [PaymentController::class, 'paypalCheckout'])->name('paypal.checkout');
    Route::get('paypal/success', [PaymentController::class, 'paypalSuccess'])->name('paypal.success');
    Route::get('paypal/cancel', [PaymentController::class, 'paypalCancel'])->name('paypal.cancel');

    Route::get('/payment/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::get('/payment/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
    Route::get('/payment/failed', [PaymentController::class, 'failed'])->name('payment.failed');

    Route::get('/vnpay/checkout', [PaymentController::class, 'vnpayCheckout'])->name('vnpay.checkout');
    Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');

    Route::get('/payment/add-credit', function () {
        return view('add-credit');
    })->name('stripe.add.credit');

    Route::post('/stripe/fund-test', function (Request $request) {
        Stripe::setApiKey(config('services.stripe.secret'));
        $amount = $request->input('amount', 2000); // Mặc định là $20.00
        $currency = $request->input('currency', 'usd'); // Mặc định là
        $charge = Charge::create([
            'amount' => $amount, // $20.00
            'currency' => $currency, // USD
            'source' => 'tok_bypassPending', // ✅ Token test để bypass xử lý
            'description' => 'Nạp tiền test vào platform balance',
        ]);
        \Log::info('Stripe test charge created', ['charge' => $charge]);
        return redirect()->back()->with('success', 'Test charge created successfully!');
    })->name('stripe.fund.test');
});

Route::get('/test-session', function () {
    // Set 1 giá trị vào session
    Session::put('user_name', 'Hoang');

    return response()->json([
        'session_id' => Session::getId(),
        'user_name' => Session::get('user_name'),
    ]);
});

Route::get('/test-cache', function () {
    // Set 1 giá trị vào cache
    Cache::put('user_name', 'Hoang');
    Cache::put('user_id', 1);
    return response()->json([
        'session_id' => Cache::get('user_name'),
        'user_name' => Cache::get('user_id'),
    ]);
});

Route::get('/test-redis', function () {
    // Set 1 giá trị vào redis
    Redis::hset('user_name', 1, 'Hoang');
    Redis::hset('email', 1, 'ZaUWu@example.com');
    return response()->json([
        'session_id' => Redis::hget('email', 1),
        'user_name' => Redis::hget('user_name', 1),
    ]);
});

Route::get('/login', function () {
    return redirect()->route('home');
})->name('login');