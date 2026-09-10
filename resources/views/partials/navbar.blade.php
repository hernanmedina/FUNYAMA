{{-- resources/views/partials/navbar.blade.php --}}
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="flex items-center">
                    <div class="mr-3">
                        <x-application-logo class="h-10 w-10 object-contain" />
                    </div>
                    <span class="text-xl font-bold text-gray-800">Fundación YAMA</span>
                </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="{{ url('/') }}#inicio" class="text-gray-700 hover:text-blue-600 font-medium">Inicio</a>
                <a href="{{ url('/') }}#cursos" class="text-gray-700 hover:text-blue-600 font-medium">Cursos</a>
                <a href="{{ url('/') }}#eventos" class="text-gray-700 hover:text-blue-600 font-medium">Eventos</a>
                <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Blog</a>
                <a href="{{ route('opiniones.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Opiniones</a>
                <a href="{{ url('/') }}#nosotros" class="text-gray-700 hover:text-blue-600 font-medium">Nosotros</a>
                <a href="{{ url('/') }}#contacto" class="text-gray-700 hover:text-blue-600 font-medium">Contacto</a>
            </div>

            <!-- Auth Links -->
            <div class="flex items-center gap-4 ml-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Mi Cuenta
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>
