{{-- resources/views/layouts/public.blade.php --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased">
<!-- Simple Header para páginas públicas -->
<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">
                    <div class="mr-3">
                        <img src="{{ asset('images/icono.png') }}"
                             alt="Logo Fundación"
                             class="h-8 w-8 object-contain">
                    </div>
                    <span class="text-xl font-bold text-gray-800">Fundación YAMA</span>
                </a>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ url('/') }}" class="text-gray-700 hover:text-purple-600">Inicio</a>
{{--                <a href="{{ route('cursos.index') }}" class="text-gray-700 hover:text-purple-600">Cursos</a>--}}
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg">Mi Cuenta</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600">Ingresar</a>
                    <a href="{{ route('register') }}" class="bg-blue-700 text-white px-4 py-2 rounded-lg">Registrarse</a>
                @endauth
            </div>
        </div>
    </div>
</nav>

<!-- Page Content -->
<main>
    {{ $slot }}
</main>

@livewireScripts
</body>
</html>
