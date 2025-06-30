@extends('layouts.app')

@section('title', 'Nạp tiền')

@section('content')
    <!-- ✅ Thông báo -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-semibold">✅ Thành công! </strong>
            <span>{{ session('success') }}</span>
        </div>
    @elseif (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-semibold">❌ Lỗi! </strong>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <div class="max-w-md mx-auto mt-10 p-8 bg-blue-50 rounded-2xl shadow-lg space-y-6">
        <h2 class="text-3xl font-bold text-center text-gray-800">💰 Nạp tiền vào tài khoản</h2>

        <!-- 🔹 Stripe Form -->
        <form action="{{ route('stripe.fund.test') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền:</label>
                <input type="number" name="amount" min="100" step="100" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Nhập số tiền (ví dụ: 2000 cho $20.00)">
            </div>

            <div>
                <label for="currency" class="block text-sm font-medium text-gray-700 mb-1">Tiền tệ:</label>
                <select name="currency"
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300">
                    <option value="usd" selected>USD</option>
                    <option value="eur">EUR</option>
                    <option value="vnd">VND</option>
                </select>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-black py-2 rounded-lg font-semibold transition">
                💳 Nạp tiền vào Stripe
            </button>
        </form>
@endsection 