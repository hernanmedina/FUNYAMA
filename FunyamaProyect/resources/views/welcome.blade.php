{{-- resources/views/welcome.blade.php --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Fundación YAMA - Enseñanzas Que Dejan Huella</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>
        .hero-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #1e3a8a 100%);
        }
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }
        .stat-number {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
<!-- Header/Navigation -->
<nav class="bg-white shadow-lg sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            {{-- En el header --}}
            <div class="flex items-center">
                <div class="mr-3">
                    <x-application-logo class="h-10 w-10 object-contain" />
                </div>
                <span class="text-xl font-bold text-gray-800">Fundación YAMA</span>
            </div>

            <!-- Navigation Links -->
            <div class="hidden md:flex space-x-8">
                <a href="#inicio" class="text-gray-700 hover:text-blue-600 font-medium">Inicio</a>
                <a href="#cursos" class="text-gray-700 hover:text-blue-600 font-medium">Cursos</a>
                <a href="#nosotros" class="text-gray-700 hover:text-blue-600 font-medium">Nosotros</a> {{-- pendiente por validar si queda haí--}}
                <a href="#contacto" class="text-gray-700 hover:text-blue-600 font-medium">Contacto</a>
            </div>

            <!-- Auth Links -->
            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('dashboard') }}"
                       class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Mi Cuenta
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="text-gray-700 hover:text-blue-600 font-medium">
                        Iniciar Sesión
                    </a>
                    <a href="{{ route('register') }}"
                       class="bg-blue-600 hover:bg-blue-800 text-white px-4 py-2 rounded-lg font-medium transition duration-200">
                        Registrarse
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>


<!-- Logo -->
<div class="flex justify-center mb-6">
    <div class="bg-white  w-64 h-64 flex items-center justify-center ">
        <x-application-mark class="h-30 w-30 object-contain" />
    </div>
</div>

<!-- Hero Section -->
<section id="inicio" class="hero-gradient text-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">




            <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                Enseñanzas Que Dejan
                <span class="text-yellow-300">Huella</span>
            </h1>
            <p class="text-xl md:text-2xl mb-8 text-purple-100 max-w-3xl mx-auto">
                Brindamos oportunidades de aprendizaje de calidad para construir un futuro mejor en nuestra comunidad.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('cursos.index') }}"
                   class="bg-white text-blue-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition duration-200 shadow-lg">
                    Explorar Cursos
                </a>
                <a href="#nosotros"
                   class="border-2 border-white text-white hover:bg-white hover:text-blue-600 px-8 py-4 rounded-lg font-bold text-lg transition duration-200">
                    Conocer Más
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-center">
            <div class="p-6">
                <div class="stat-number text-4xl font-bold mb-2">235+</div>
                <p class="text-gray-600 font-medium">Estudiantes Beneficiados</p>
            </div>
            <div class="p-6">
                <div class="stat-number text-4xl font-bold mb-2">20+</div>
                <p class="text-gray-600 font-medium">Cursos Disponibles</p>
            </div>
            <div class="p-6">
                <div class="stat-number text-4xl font-bold mb-2">15+</div>
                <p class="text-gray-600 font-medium">Años de Experiencia</p>
            </div>
            <div class="p-6">
                <div class="stat-number text-4xl font-bold mb-2">98%</div>
                <p class="text-gray-600 font-medium">Satisfacción </p>
            </div>
        </div>
    </div>
</section>

<!-- Courses Section -->
<section id="cursos" class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Nuestros Programas Educativos</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Ofrecemos una amplia variedad de cursos, Capacitaciones, Diplomados, Conferencias y Talleres  diseñados para desarrollar habilidades y competencias para el mundo actual.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Course Card 1 -->
            <div class="bg-white rounded-xl shadow-lg card-hover p-6">
                <div class="bg-blue-100 p-3 rounded-lg w-12 h-12 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Herramientas Ofimáticas</h3>
                <p class="text-gray-600 mb-4">
                    Aprende las habilidades digitales más demandadas en el mercado laboral actual.
                </p>
                <ul class="text-gray-600 space-y-2 mb-6">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Word, Excel, PowerPoint Básico
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Word, Excel, PowerPoint Intermedio
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Word, Excel, PowerPoint Avanzado...
                    </li>
                </ul>
                <a href="{{ route('cursos.index') }}" class="text-blue-600 font-semibold hover:text-blue-700 flex items-center">
                    Ver cursos
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Course Card 2 -->
            <div class="bg-white rounded-xl shadow-lg card-hover p-6">
                <div class="bg-green-100 p-3 rounded-lg w-12 h-12 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Negocios y Emprendimiento</h3>
                <p class="text-gray-600 mb-4">
                    Desarrolla habilidades empresariales para crear y gestionar tu propio negocio.
                </p>
                <ul class="text-gray-600 space-y-2 mb-6">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Administración y Estrategia Empresarial
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Finanzas Personales
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Gestión de Proyectos...
                    </li>
                </ul>
{{--                Aqui debería de enrutar hacia la pagina de coferencias cambiar cursos.index por conferencias.index--}}
                <a href="{{ route('cursos.index') }}" class="text-green-600 font-semibold hover:text-green-700 flex items-center">
                    Ver Conferencias y Capacitaciones
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>

            <!-- Course Card 3 -->
            <div class="bg-white rounded-xl shadow-lg card-hover p-6">
                <div class="bg-purple-100 p-3 rounded-lg w-12 h-12 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-3">Desarrollo Personal</h3>
                <p class="text-gray-600 mb-4">
                    Mejora tus habilidades blandas y desarrolla tu máximo potencial personal y profesional.
                </p>
                <ul class="text-gray-600 space-y-2 mb-6">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Liderazgo y Comunicación
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Inteligencia Emocional
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        La Inteligencia Vs La Disciplina...
                    </li>
                </ul>
                {{--                Aqui debería de enrutar hacia la pagina de talleres cambiar cursos.index por talleres.index--}}
                <a href="{{ route('cursos.index') }}" class="text-purple-600 font-semibold hover:text-purple-700 flex items-center">
                    Ver Talleres
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="nosotros" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>


                <h2 class="text-4xl font-bold text-gray-800 mb-6">Misión</h2>
                <p class="text-lg text-gray-600 mb-6">
                    La <span class="font-semibold text-purple-600">Fundación YAMA</span>, está comprometida a proveer programas en educación, conservación del medio ambiente y la preservación de la cultura con el fin de mejorar, enriquecer y contribuir a la calidad de vida de los menos privilegiados,
                    promoviendo el desarrollo de las personas a las que nos dirigimos y generando nuevas oportunidades garantizando el acceso a la educación en aquellos colectivos más desfavorecidos que se encuentran en riesgo de exclusión social, promoviendo
                    personas integrales, dando respuesta a todas sus necesidades personales, educativas y sociolaborales.
                </p>
                <p class="text-lg text-gray-600 mb-8">


                </p>

            </div>

            {{--Vison Fndación YAMA--}}
            <div>
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Visión</h2>
                <p class="text-lg text-gray-600 mb-6">
                    Al 2027 ser una organización reconocida por el impacto de nuestras actuaciones en
                    el bienestar y el desarrollo de las comunidades con las que trabajamos así como
                    por nuestra integridad y profesionalidad en el modo de actuar; ser reconocidos por
                    ofrecer respuestas adecuadas a las necesidades de formación integral e impulsar
                    acciones dedicadas a la cooperación para alcanzar el necesario desarrollo humano
                    y material, como resultado de nuestro compromiso social con aquellas personas y
                    pueblos más desfavorecidos.
                </p>
                <p class="text-lg text-gray-600 mb-8">


                </p>

            </div>


            <div class="items-center ">
                <div class="bg-gradient-to-br from-blue-500 to-pink-500 rounded-2xl p-8 text-white">
                    <div class="text-center">
                        <div class="text-6xl font-bold mb-4">15+</div>
                        <div class="text-xl font-semibold">Años Transformando Vidas</div>
                        <p class="mt-4 text-purple-100">
                            Más de una década comprometidos con la excelencia educativa y el desarrollo comunitario.
                        </p>
                    </div>
                </div>
            </div>

            {{--Items--}}
            <div class="bg-gradient-to-br from-blue-500 to-pink-500 rounded-2xl p-8 text-white">
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-full mr-4 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-white-700 font-medium">Educación accesible para todos</span>
                </div>
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-full mr-4 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-white-700 font-medium">Instructores altamente calificados</span>
                </div>
                <div class="flex items-center">
                    <div class="bg-green-100 p-2 rounded-full mr-4 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <span class="text-white-700 font-medium">Comunidad de apoyo y crecimiento</span>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="hero-gradient py-16">
    <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-6">
            ¿Listo para comenzar tu aprendizaje?
        </h2>
        <p class="text-xl text-purple-100 mb-8">
            Únete a nuestra comunidad y descubre cómo la educación puede transformar tu vida.
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @auth
                <a href="{{ route('dashboard') }}"
                   class="bg-white text-purple-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition duration-200 shadow-lg">
                    Ir a Mi Dashboard
                </a>
            @else
                <a href="{{ route('register') }}"
                   class="bg-white text-purple-600 hover:bg-gray-100 px-8 py-4 rounded-lg font-bold text-lg transition duration-200 shadow-lg">
                    Crear Cuenta Gratis
                </a>
                <a href="{{ route('login') }}"
                   class="border-2 border-white text-white hover:bg-white hover:text-purple-600 px-8 py-4 rounded-lg font-bold text-lg transition duration-200">
                    Iniciar Sesión
                </a>
            @endauth
        </div>
    </div>
</section>

<!-- Footer -->
<footer id="contacto" class="bg-gray-800 text-white py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
                <div class="flex items-center mb-4">
                    <div class="flex items-center">
                        <div class="bg-white rounded-full mr-3">
                            <x-application-logo class="h-20 w-20 object-contain" />
                                  {{-- Tamaño más pequeño para el círculo --}}
                        </div>
                    </div>
                    <span class="text-xl font-bold">Fundación YAMA</span>
                </div>
                <p class="text-gray-400">
                    Enseñanzas Que Dejan Huella.
                </p>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Enlaces Rápidos</h3>
                <ul class="space-y-2 text-gray-400">
                    <li><a href="#inicio" class="hover:text-white transition">Inicio</a></li>
                    <li><a href="#cursos" class="hover:text-white transition">Cursos</a></li>
                    <li><a href="#nosotros" class="hover:text-white transition">Nosotros</a></li>
                    <li><a href="{{ route('cursos.index') }}" class="hover:text-white transition">Catálogo</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                <ul class="space-y-2 text-gray-400">
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        info@fundacioneducativa.org
                    </li>
                    <li class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        +1 (555) 123-4567
                    </li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Síguenos</h3>
                <div class="flex space-x-4">
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <span class="sr-only">Facebook</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-400 hover:text-white transition">
                        <span class="sr-only">Instagram</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.988 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.637.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.49-3.323-1.297C4.318 14.865 3.828 13.714 3.828 12.417s.49-2.448 1.297-3.323c.875-.807 2.026-1.297 3.323-1.297s2.448.49 3.323 1.297c.807.875 1.297 2.026 1.297 3.323s-.49 2.448-1.297 3.323c-.875.807-2.026 1.297-3.323 1.297z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
            <p>&copy; 2024 Todos los derechos reservados.</p>
        </div>
    </div>
</footer>

<!-- Smooth Scroll -->
<script>
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });
</script>
</body>
</html>
