@extends('layouts.app')

@section('title', 'Thanh toán thất bại')

@section('content')
<!-- ✅ Thông báo -->
    @if (session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-6" role="alert">
            <strong class="font-semibold">❌ Lỗi! </strong>
            <span>{{ session('error') }}</span>
        </div>
    @endif
    <div class="max-w-md mx-auto mt-16 p-6 bg-white rounded-lg shadow-md text-center">
        <h2 class="text-2xl font-bold text-red-600 mb-4">❌ Thanh toán thất bại</h2>
        <p class="text-gray-700">Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.</p>

        <a href="{{ route('payment.page') }}"
           class="mt-6 inline-block bg-indigo-600 text-black px-4 py-2 rounded hover:bg-indigo-700 transition">
            Thử lại
        </a>
    </div>
@endsection

