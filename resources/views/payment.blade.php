@extends('layouts.app')

@section('title', 'Thanh toán')

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
        <h2 class="text-3xl font-bold text-center text-gray-800">💳 Chọn phương thức thanh toán</h2>

        <!-- 🔹 Stripe Form -->
        <form action="{{ route('stripe.checkout') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div>
                <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Mã đơn hàng:</label>
                <input type="number" name="order_id" min="1" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Nhập mã đơn hàng">
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền (USD):</label>
                <input type="number" name="amount" min="1" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Nhập số tiền USD">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-black py-2 rounded-lg font-semibold transition">
                💳 Thanh toán bằng Stripe
            </button>
        </form>

        <!-- 🔹 PayPal -->
        <form action="{{ route('paypal.checkout') }}" method="POST" class="space-y-4 bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div>
                <label for="order_id" class="block text-sm font-medium text-gray-700 mb-1">Mã đơn hàng:</label>
                <input type="number" name="order_id" min="1" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Nhập mã đơn hàng">
            </div>

            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền (USD):</label>
                <input type="number" name="amount" min="1" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Nhập số tiền USD">
            </div>

            <div>
                <label for="vendor_email" class="block text-sm font-medium text-gray-700 mb-1">Email người bán:</label>
                <input type="email" name="vendor_email" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-indigo-300"
                    placeholder="Nhập email người bán" value="sb-vihw044180697@business.example.com">
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-black py-2 rounded-lg font-semibold transition">
                 🅿️ Thanh toán bằng PayPal
            </button>
        </form>

        <!-- 🔹 VNPAY -->
        <form method="GET" action="{{ route('vnpay.checkout') }}" class="space-y-4 bg-white p-6 rounded-lg shadow-md">
            @csrf
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Số tiền (VND):</label>
                <input type="number" name="amount" min="10000" step="1000" required
                    class="w-full border border-gray-300 rounded-lg p-2 focus:outline-none focus:ring focus:ring-red-300"
                    placeholder="Nhập số tiền VND">
            </div>
            <button type="submit"
                class="w-full bg-red-500 hover:bg-red-600 text-black py-2 rounded-lg font-semibold transition">
                🇻🇳 Thanh toán qua VNPAY
            </button>
        </form>
    </div>
@endsection
