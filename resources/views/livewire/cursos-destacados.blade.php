<!-- Cursos Destacados Section -->
<section id="cursos" class="bg-white py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Nuestros Programas Educativos</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Ofrecemos una amplia variedad de cursos, capacitaciones, diplomados, conferencias y talleres diseñados para desarrollar habilidades y competencias para el mundo actual.
            </p>
        </div>

        @if($cursos->count() > 0)
            <!-- Contenedor con scroll horizontal -->
            <style>
                .scroll-cursos::-webkit-scrollbar { height: 6px; }
                .scroll-cursos::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
                .scroll-cursos::-webkit-scrollbar-thumb { background: #93c5fd; border-radius: 10px; }
                .scroll-cursos::-webkit-scrollbar-thumb:hover { background: #60a5fa; }
            </style>
            <div class="relative group">
                <!-- Scroll Container -->
                <div class="scroll-cursos flex overflow-x-auto gap-6 pb-6 snap-x snap-mandatory" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch; scrollbar-width: thin; scrollbar-color: #93c5fd #f1f5f9;">
                    @foreach($cursos as $curso)
                        <div class="snap-start flex-shrink-0 w-[340px] bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Course Image -->
                        <div class="relative h-48 bg-gradient-to-br from-blue-500 to-indigo-600 overflow-hidden">
                            @if($curso->imagen_portada)
                                <img src="{{ asset('storage/' . $curso->imagen_portada) }}" 
                                     alt="{{ $curso->nombre }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Level Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold
                                    {{ $curso->nivel === 'avanzado' ? 'bg-red-600 text-white' : 
                                       ($curso->nivel === 'intermedio' ? 'bg-yellow-500 text-white' : 'bg-green-500 text-white') }}">
                                    {{ ucfirst($curso->nivel ?? 'Básico') }}
                                </span>
                            </div>

                            <!-- Destacado Badge -->
                            @if($curso->destacado)
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center gap-1 bg-yellow-400 text-yellow-900 px-3 py-1 rounded-full text-xs font-semibold">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                        </svg>
                                        Destacado
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Course Content -->
                        <div class="p-6">
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                                {{ $curso->nombre }}
                            </h3>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($curso->descripcion, 100) }}
                            </p>

                            <!-- Course Details -->
                            <div class="space-y-3 mb-6">
                                <!-- Duration -->
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-medium text-sm">
                                        {{ $curso->duracion_texto ?? ($curso->duracion_horas ? $curso->duracion_horas . ' horas' : 'Duración flexible') }}
                                    </span>
                                </div>

                                <!-- Students Count -->
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <span class="font-medium text-sm">
                                        {{ $curso->estudiantes->count() }} Estudiantes
                                    </span>
                                </div>

                                <!-- Available Spots -->
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-12 0z"/>
                                    </svg>
                                    <span class="font-medium text-sm">
                                        {{ $curso->cupo_disponible }} / {{ $curso->cupo_total }} Cupos
                                    </span>
                                </div>

                            <!-- Price -->
                            <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                                @if($curso->precio_descuento)
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg font-bold text-blue-700">${{ number_format($curso->precio_descuento, 2) }} COP</span>
                                        <span class="text-sm text-gray-500 line-through">${{ number_format($curso->precio_regular, 2) }}</span>
                                        @php $descuento = (($curso->precio_regular - $curso->precio_descuento) / $curso->precio_regular) * 100; @endphp
                                        <span class="text-xs font-semibold text-red-600 bg-red-100 px-2 py-0.5 rounded-full">-{{ round($descuento) }}%</span>
                                    </div>
                                @else
                                    <span class="text-lg font-bold text-blue-700">${{ number_format($curso->precioFinal, 2) }} COP</span>
                                @endif
                            </div>
                            </div>

                            <!-- CTA Button -->
                            <a href="{{ route('cursos.show', $curso->codigo) }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition duration-200 flex items-center justify-center">
                                Ver Curso
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
                </div>

                <!-- Flechas de navegación -->
                <button onclick="this.parentElement.querySelector('.scroll-cursos').scrollBy({left: -360, behavior: 'smooth'})" 
                        class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 bg-white shadow-lg rounded-full p-2 hover:bg-gray-100 transition opacity-0 group-hover:opacity-100 hidden md:block z-10">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button onclick="this.parentElement.querySelector('.scroll-cursos').scrollBy({left: 360, behavior: 'smooth'})" 
                        class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 bg-white shadow-lg rounded-full p-2 hover:bg-gray-100 transition opacity-0 group-hover:opacity-100 hidden md:block z-10">
                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

            <!-- Indicadores de scroll (dots) -->
            <div class="flex justify-center gap-2 mt-4 md:hidden">
                @foreach($cursos as $index => $curso)
                    <button onclick="this.closest('section').querySelector('.scroll-cursos').children[{{ $index }}].scrollIntoView({behavior:'smooth',block:'nearest',inline:'center'})" 
                            class="w-2.5 h-2.5 rounded-full bg-blue-300 hover:bg-blue-500 transition"></button>
                @endforeach
            </div>

            <!-- View All Courses Button -->
            <div class="mt-12 text-center">
                <a href="{{ route('cursos.index') }}" 
                   class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200 shadow-lg">
                    Explorar Todos los Cursos
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>

        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center border border-gray-200">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay cursos disponibles</h3>
                <p class="text-gray-600 mb-6">
                    Por el momento no hay cursos publicados. Vuelve pronto para ver nuevos programas educativos.
                </p>
                <a href="{{ route('cursos.index') }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Ver Catálogo de Cursos
                </a>
            </div>
        @endif
    </div>
</section>
