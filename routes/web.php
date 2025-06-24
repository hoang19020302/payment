<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

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
    event(new \App\Events\MessageSent('Hello private from backend', auth()->id()));
    return 'Sent!';
});
