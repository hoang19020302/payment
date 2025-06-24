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
    <title>Sneaker Store</title>

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
<body class="font-sans antialiased bg-white text-gray-800">

    {{-- Main Content --}}
    <main>
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-gray-800 text-white py-4">
        <div class="container mx-auto text-center">
            <p>&copy; {{ date('Y') }} Sneaker Store. All rights reserved.</p>
            <p>Follow us on 
                <a href="https://facebook.com" class="text-blue-500 hover:underline">Facebook</a>, 
                <a href="https://twitter.com" class="text-blue-500 hover:underline">Twitter</a>, 
                <a href="https://instagram.com" class="text-blue-500 hover:underline">Instagram</a>
            </p>
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
