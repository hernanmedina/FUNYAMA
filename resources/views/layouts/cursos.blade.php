{{-- resources/views/layouts/cursos.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Funyama') }} - Cursos</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Livewire Styles -->
    @livewireStyles
</head>
<body class="font-sans antialiased bg-gray-50">

    <!-- Header público -->
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
                    <a href="{{ route('cursos.index') }}" class="text-purple-600 font-semibold">Cursos</a>
                    <a href="{{ route('eventos.index') }}" class="text-gray-700 hover:text-purple-600">Eventos</a>
                    @auth
                        <a href="{{ route('dashboard') }}" class="bg-purple-600 text-white px-4 py-2 rounded-lg">Mi Cuenta</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-purple-600">Ingresar</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero de la página de cursos -->
    <div class="bg-gradient-to-r from-blue-700 to-blue-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Nuestros Cursos</h1>
            <p class="text-xl text-blue-200 max-w-2xl mx-auto">
                Explora nuestra oferta educativa y encuentra el curso perfecto para ti
            </p>
        </div>
    </div>

    <!-- Page Content -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p>&copy; {{ date('Y') }} Fundación YAMA. Todos los derechos reservados.</p>
        </div>
    </footer>

    @livewireScripts

    {{-- Script inline para forzar la recarga de Livewire si está cacheado --}}
    <script>
        document.addEventListener('livewire:init', function () {
            console.log('[Livewire] Inicializado correctamente');
        });
    </script>
</body>
</html>
