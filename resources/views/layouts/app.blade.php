<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Page Title --}}
    <meta name="description" content="Sneaker Store - Your one-stop shop for the latest sneakers and streetwear.">
    <meta name="keywords" content="sneakers, streetwear, sneaker store, buy sneakers, sneaker shop">
    <meta name="author" content="Sneaker Store Team">
    <meta name="theme-color" content="#ffffff">
    <title>@yield('title')</title>

    {{-- Favicon --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Tailwind CSS --}}
    @vite('resources/css/app.css')

    {{-- Livewire Styles --}}
    @livewireStyles()

</head>
<body class="font-sans antialiased text-gray-800">

    <header class="bg-white shadow mb-10 py-4">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <!-- Can giua header -->
            <h1 class="text-xl font-bold text-gray-800 text-center">Hệ thống thanh toán</h1>
        </div>
    </header>

    {{-- Main Content --}}
    <!-- Chiem  80% chieu cao man hinh , chieu rong 100% -->
    <div class="min-h-screen max-w-7xl mx-auto flex flex-col">
    <main class="flex-1 px-4 py-6">
        {{-- Flash Messages --}}
        {{-- Navigation --}}
        <nav class="mb-6 bg-gray-100 p-4 rounded-lg shadow flex justify-center">
            <ul class="flex space-x-4">
                <li><a href="{{ route('payment.page') }}" class="text-blue-600 hover:underline">Thanh toán  |</a></li>
                <li><a href="{{ route('stripe.add.credit') }}" class="text-blue-600 hover:underline">| Nạp tiền Stripe</a></li>
            </ul>
        </nav>
        @yield('content')
    </main>
    </div>
<!-- Day footer xuong cuoi trang -->
    <footer class="bg-white bg-gray-100 shadow mt-10 py-6 border-t">
        {{-- Footer Content --}}
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-sm text-gray-600">Hệ thống thanh toán Laravel Demo</p>
            <div class="text-xs text-gray-500 mt-2">
                <a href="#" class="hover:underline">Chi sách đơn hàng</a>   
                &copy; {{ now()->year }} - Thanh toán Laravel Demo
        </div>
    </footer>

    {{-- Scripts --}}
    @yield('scripts')

    {{-- Livewire Scripts --}}
    @livewireScripts()

    {{-- Alpine.js --}}
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    {{-- Vite JS --}}
    @vite('resources/js/app.js')
</body>
</html>
