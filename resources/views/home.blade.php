@extends('layouts.app')

@section('content')
    <section class="relative w-full h-screen overflow-hidden">
        <video autoplay loop muted playsinline class="absolute w-full h-full object-cover z-0">
            <source src="{{ asset('videos/sneakers.mp4') }}" type="video/mp4">
            Trình duyệt không hỗ trợ video.
        </video>
        <div class="absolute inset-0 bg-black/50 z-10 flex items-center justify-center">
            <h1 class="text-white text-4xl md:text-6xl font-bold text-center">Modern Sneakers</h1>
        </div>
    </section>

    <section class="bg-gray-100">
        @livewire('product-list')
    </section>
@endsection
