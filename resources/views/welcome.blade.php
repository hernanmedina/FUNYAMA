{{-- resources/views/welcome.blade.php --}}
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Fundación YAMA -ñanzas Que Dejan Huella</title>

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
                <a href="#eventos" class="text-gray-700 hover:text-blue-600 font-medium">Eventos</a>
                <a href="{{ route('blog.index') }}" class="text-gray-700 hover:text-blue-600 font-medium">Blog</a>
                <a href="#nosotros" class="text-gray-700 hover:text-blue-600 font-medium">Nosotros</a>
                <a href="#contacto" class="text-gray-700 hover:text-blue-600 font-medium">Contacto</a>
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


<!-- Logo -->
<div class="flex justify-center mb-6">
    <div class="bg-white w-[700px] h-[auto] mt-8 flex items-center justify-center ">
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
                <a href="#cursos"
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

<!-- Courses Section con Livewire -->
@livewire("cursos-destacados")

<!-- Eventos Destacados Section con Livewire -->
@livewire('eventos-destacados')

<!-- About Section -->
<section id="nosotros" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">
            
            {{--Mision Fndación YAMA--}}
            <div class="flex flex-col h-full">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Misión</h2>
                <p class="text-lg leading-8 text-gray-600 mb-6 justify-text">
                    La <span class="font-semibold text-purple-600">Fundación YAMA</span>, está comprometida a proveer programas en educación, conservación del medio ambiente y la preservación de la cultura con el fin de mejorar, enriquecer y contribuir a la calidad de vida de los menos privilegiados,
                    promoviendo el desarrollo de las personas a las que nos dirigimos y generando nuevas oportunidades garantizando el acceso a la educación en aquellos colectivos más desfavorecidos que se encuentran en riesgo de exclusión social, promoviendo
                    personas integrales, dando respuesta a todas sus necesidades personales, educativas y sociolaborales.
                </p>
                <p class="text-lg text-gray-600 mb-8">
                </p>

            </div>

            {{--Vison Fndación YAMA--}}
            <div class="flex flex-col h-full">
                <h2 class="text-4xl font-bold text-gray-800 mb-6">Visión</h2>
                <p class="text-lg leading-8 text-gray-600 mb-6 justify-text">
                    Al 2030 ser una organización reconocida por el impacto de nuestras actuaciones en
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
                    <li><a href="#Eventos" class="hover:text-white transition">Eventos</a></li>
                    <li><a href="#nosotros" class="hover:text-white transition">Nosotros</a></li>
                    <li><a href="{{ route('cursos.index') }}" class="hover:text-white transition">Catálogo</a></li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Contacto</h3>
                <div class="flex space-x-4">
                    <!-- Correo -->
                <a href="mailto:fundacionyamacapacitaciones@gmail.com"
                class="text-gray-400 hover:text-white transition">
                    <span class="sr-only">Correo electrónico</span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </a>

                <!-- WhatsApp 1 -->
                <a href="https://wa.me/573233731395"
                target="_blank"
                rel="noopener noreferrer"
                class="text-gray-400 hover:text-white transition">
                    <span class="sr-only">WhatsApp 323 373 1395</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.53 0 .23 5.3.23 11.81c0 2.08.54 4.11 1.57 5.9L.13 24l6.43-1.69a11.8 11.8 0 005.48 1.39h.01c6.51 0 11.81-5.3 11.81-11.81 0-3.15-1.23-6.11-3.34-8.41zM12.05 21.7h-.01a9.88 9.88 0 01-5.04-1.38l-.36-.21-3.82 1 1.02-3.72-.24-.38a9.84 9.84 0 01-1.51-5.2C2.09 6.36 6.55 1.9 12.05 1.9a9.84 9.84 0 019.84 9.84c0 5.49-4.46 9.96-9.84 9.96z"/>
                        <path d="M17.1 14.35c-.28-.14-1.65-.81-1.91-.9-.26-.1-.45-.14-.64.14-.19.28-.73.9-.89 1.09-.16.19-.33.21-.61.07-.28-.14-1.18-.43-2.25-1.39-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.16.19-.28.28-.47.09-.19.05-.35-.02-.49-.07-.14-.64-1.54-.88-2.11-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.49.07-.75.35-.26.28-.98.96-.98 2.34 0 1.38 1 2.71 1.14 2.89.14.19 1.97 3.01 4.78 4.22.67.29 1.19.46 1.6.59.67.21 1.28.18 1.76.11.54-.08 1.65-.67 1.88-1.32.23-.65.23-1.21.16-1.32-.07-.12-.26-.19-.54-.33z"/>
                    </svg>
                </a>

                <!-- WhatsApp 2 -->
                <a href="https://wa.me/573218821641"
                target="_blank"
                rel="noopener noreferrer"
                class="text-gray-400 hover:text-white transition">
                    <span class="sr-only">WhatsApp 321 882 1641</span>
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.52 3.48A11.78 11.78 0 0012.04 0C5.53 0 .23 5.3.23 11.81c0 2.08.54 4.11 1.57 5.9L.13 24l6.43-1.69a11.8 11.8 0 005.48 1.39h.01c6.51 0 11.81-5.3 11.81-11.81 0-3.15-1.23-6.11-3.34-8.41zM12.05 21.7h-.01a9.88 9.88 0 01-5.04-1.38l-.36-.21-3.82 1 1.02-3.72-.24-.38a9.84 9.84 0 01-1.51-5.2C2.09 6.36 6.55 1.9 12.05 1.9a9.84 9.84 0 019.84 9.84c0 5.49-4.46 9.96-9.84 9.96z"/>
                        <path d="M17.1 14.35c-.28-.14-1.65-.81-1.91-.9-.26-.1-.45-.14-.64.14-.19.28-.73.9-.89 1.09-.16.19-.33.21-.61.07-.28-.14-1.18-.43-2.25-1.39-.83-.74-1.39-1.65-1.55-1.93-.16-.28-.02-.43.12-.57.13-.13.28-.33.42-.49.14-.16.19-.28.28-.47.09-.19.05-.35-.02-.49-.07-.14-.64-1.54-.88-2.11-.23-.55-.47-.48-.64-.49h-.55c-.19 0-.49.07-.75.35-.26.28-.98.96-.98 2.34 0 1.38 1 2.71 1.14 2.89.14.19 1.97 3.01 4.78 4.22.67.29 1.19.46 1.6.59.67.21 1.28.18 1.76.11.54-.08 1.65-.67 1.88-1.32.23-.65.23-1.21.16-1.32-.07-.12-.26-.19-.54-.33z"/>
                    </svg>
                </a>
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold mb-4">Síguenos</h3>
                <div class="flex space-x-4">
                    <a href="https://youtube.com/@fundacionyamacapacitacione6929" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                        <span class="sr-only">YouTube</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </a>
                    <a href="https://www.tiktok.com/@profebarragan" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                        <span class="sr-only">TikTok</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.77 0 2.89 2.89 0 0 1 2.89-2.89h.73V9.66h-.73a6.34 6.34 0 1 0 6.34 6.34V8.14a7.22 7.22 0 0 0 3.77 1.09V6.69z"/>
                        </svg>
                    </a>
                    <a href="https://www.facebook.com/profile.php?id=100086488552580" target="_blank" rel="noopener noreferrer" class="text-gray-400 hover:text-white transition">
                        <span class="sr-only">Facebook</span>
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
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
