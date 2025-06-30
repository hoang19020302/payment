@extends('layouts.app')

@section('title', 'Thanh toán thành công')

@section('content')
    <!-- ✅ Thông báo -->
    @if (session('success'))
        <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-semibold">✅ Thành công! </strong>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- Thanh toán thanh cong -->
    <div class="max-w-md mx-auto mt-16 p-6 bg-white rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-bold text-green-600 mb-4">🎉 Thanh toán thành công!</h2>
        <p class="text-gray-700">Cảm ơn bạn đã hoàn tất thanh toán. Đơn hàng của bạn đang được xử lý.</p>

        <a href="{{ route('payment.page') }}"
           class="mt-6 inline-block bg-indigo-600 text-black px-4 py-2 rounded hover:bg-indigo-700 transition">
            Quay lại trang thanh toán
        </a>
    </div>
@endsection