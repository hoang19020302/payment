<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PaymentController;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Charge;

Route::middleware('ngrok.https')->group(function () {
    Route::get('/', function () {
        return view('home');
    });

    Route::get('/fake-login', function () {
        // Đăng nhập user đầu tiên (tạo user nếu chưa có)
        $user = User::first() ?? User::factory()->create(['name' => 'Test User']);
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