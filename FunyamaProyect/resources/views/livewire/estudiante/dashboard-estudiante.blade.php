<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Mi Dashboard</h1>
                <p class="text-gray-600 mt-2">Bienvenido/a, {{ Auth::user()->name }} {{ Auth::user()->apellido }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    Estudiante
                </span>
                <a href="{{ route('admin.estudiantes.certificados') }}"
                   class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Mis Certificados
                </a>
                <a href="{{ route('cursos.index') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Ver Todos los Cursos
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Cursos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_cursos'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Completados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['cursos_completados'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Progreso</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['cursos_en_progreso'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Progreso Promedio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ round($estadisticas['promedio_progreso'] ?? 0) }}%</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Mis Cursos Inscritos -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Mis Cursos Inscritos</h2>
                </div>
                <div class="p-6">
                    @if($cursosInscritos && $cursosInscritos->count() > 0)
                        <div class="space-y-4">
                            @foreach($cursosInscritos as $curso)
                                <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center space-x-4">
                                        @if($curso->imagen_portada)
                                            <img src="{{ asset('storage/' . $curso->imagen_portada) }}"
                                                 alt="{{ $curso->nombre }}"
                                                 class="w-12 h-12 rounded-lg object-cover">
                                        @else
                                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium text-gray-900">{{ $curso->nombre }}</h3>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                    {{ $curso->pivot->estado === 'completado' ? 'bg-green-100 text-green-800' :
                                                       ($curso->pivot->estado === 'en_progreso' ? 'bg-blue-100 text-blue-800' :
                                                       'bg-gray-100 text-gray-800') }}">
                                                    {{ ucfirst($curso->pivot->estado) }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    Progreso: {{ $curso->pivot->progreso ?? 0 }}%
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('cursos.index') }}"
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Continuar
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('cursos.index') }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Ver todos mis cursos →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No estás inscrito en ningún curso</h3>
                            <p class="mt-1 text-sm text-gray-500">Comienza explorando nuestros cursos disponibles.</p>
                            <div class="mt-6">
                                <a href="{{ route('cursos.index') }}"
                                   class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Explorar Cursos
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Cursos Disponibles -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Cursos Disponibles</h2>
                </div>
                <div class="p-6">
                    @if($cursosDisponibles && $cursosDisponibles->count() > 0)
                        <div class="grid grid-cols-1 gap-4">
                            @foreach($cursosDisponibles as $curso)
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-medium text-gray-900">{{ $curso->nombre }}</h3>
                                            <p class="text-sm text-gray-500 mt-1 line-clamp-2">
                                                {{ Str::limit($curso->descripcion, 80) }}
                                            </p>
                                            <div class="flex items-center space-x-4 mt-2">
                                                <span class="text-sm text-gray-600">
                                                    <strong>Duración:</strong> {{ $curso->duracion_texto }}
                                                </span>
                                                <span class="text-sm text-gray-600">
                                                    <strong>Nivel:</strong> {{ ucfirst($curso->nivel) }}
                                                </span>
                                            </div>
                                            <div class="flex items-center justify-between mt-3">
                                                <span class="text-lg font-bold text-gray-900">
                                                    ${{ number_format($curso->precioFinal, 2) }}
                                                </span>
                                                <span class="text-sm text-gray-500">
                                                    {{ $curso->cupo_disponible }} cupos disponibles
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex space-x-2">
                                        <button wire:click="inscribirCurso('{{ $curso->codigo }}')"
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                            Inscribirse
                                        </button>
                                        <a href="{{ route('cursos.index') }}"
                                           class="bg-gray-200 hover:bg-gray-300 text-gray-700 py-2 px-4 rounded-lg text-sm font-medium transition-colors">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('cursos.index') }}"
                               class="text-blue-600 hover:text-blue-800 font-medium">
                                Ver todos los cursos disponibles →
                            </a>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay cursos disponibles</h3>
                            <p class="mt-1 text-sm text-gray-500">Todos los cursos están llenos o no hay cursos publicados.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progreso General -->
        @if($cursosInscritos && $cursosInscritos->count() > 0)
            <div class="mt-8 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Mi Progreso de Aprendizaje</h2>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-700">Progreso General</span>
                        <span class="text-sm font-bold text-blue-600">{{ round($estadisticas['promedio_progreso'] ?? 0) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4">
                        <div class="bg-blue-600 h-4 rounded-full transition-all duration-300"
                             style="width: {{ $estadisticas['promedio_progreso'] ?? 0 }}%"></div>
                    </div>
                    <div class="grid grid-cols-3 gap-4 mt-6 text-center">
                        <div>
                            <div class="text-2xl font-bold text-green-600">{{ $estadisticas['cursos_completados'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Completados</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $estadisticas['cursos_en_progreso'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">En Progreso</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-600">{{ $estadisticas['total_cursos'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600">Total Inscritos</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
