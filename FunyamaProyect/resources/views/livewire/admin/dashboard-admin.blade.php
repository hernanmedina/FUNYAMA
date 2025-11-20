<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Dashboard Administrativo</h1>
                <p class="text-gray-600 mt-2">Bienvenido/a, {{ Auth::user()->name }} {{ Auth::user()->apellido }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <!-- Filtro de rango de fechas -->
                <select wire:model.live="rangoFechas"
                        class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="7">Últimos 7 días</option>
                    <option value="30">Últimos 30 días</option>
                    <option value="90">Últimos 90 días</option>
                    <option value="365">Último año</option>
                </select>

                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                    Administrador
                </span>
            </div>
        </div>

        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Cursos -->
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
                        <p class="text-xs text-green-600 mt-1">
                            +{{ $estadisticas['nuevos_cursos'] ?? 0 }} nuevos
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Estudiantes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Estudiantes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_estudiantes'] ?? 0 }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            +{{ $estadisticas['nuevos_estudiantes'] ?? 0 }} nuevos
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ingresos -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Ingresos Totales</p>
                        <p class="text-2xl font-bold text-gray-900">${{ number_format($estadisticas['ingresos_totales'] ?? 0, 2) }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            +${{ number_format($estadisticas['ingresos_recientes'] ?? 0, 2) }} recientes
                        </p>
                    </div>
                </div>
            </div>

            <!-- Solicitudes Pendientes -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Solicitudes Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['solicitudes_pendientes'] ?? 0 }}</p>
                        <p class="text-xs text-green-600 mt-1">
                            {{ $estadisticas['solicitudes_resueltas'] ?? 0 }} resueltas
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Acciones Rápidas -->
            <!-- Acciones Rápidas -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Acciones Rápidas</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Nuevo Curso -->
                        <a href="{{ route('admin.cursos.create') }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24">
                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            <span class="text-sm font-medium">Nuevo Curso</span>
                        </a>

                        <!-- Gestionar Cursos -->
                        <a href="{{ route('admin.cursos.index') }}"
                           class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24">
                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="text-sm font-medium">Gestionar Cursos</span>
                        </a>

                        <!-- Gestionar Estudiantes -->
                        <a href="#"
                           class="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24 opacity-70 cursor-not-allowed">
                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Gestionar Estudiantes</span>
                        </a>

                        <!-- Reportes -->
                        <a href="#"
                           class="bg-orange-600 hover:bg-orange-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24 opacity-70 cursor-not-allowed">
                            <svg class="w-6 h-6 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="text-sm font-medium">Reportes</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Cursos Más Populares -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Cursos Más Populares</h2>
                </div>
                <div class="p-6">
                    @if(isset($estadisticas['cursos_populares']) && $estadisticas['cursos_populares']->count() > 0)
                        <div class="space-y-4">
                            @foreach($estadisticas['cursos_populares'] as $curso)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        @if($curso->imagen_portada)
                                            <img src="{{ asset('storage/' . $curso->imagen_portada) }}"
                                                 alt="{{ $curso->nombre }}"
                                                 class="w-10 h-10 rounded-lg object-cover">
                                        @else
                                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center">
                                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="font-medium text-gray-900 text-sm">{{ Str::limit($curso->nombre, 30) }}</h3>
                                            <p class="text-xs text-gray-500">{{ $curso->estudiantes_count }} estudiantes</p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button wire:click="togglePublicacion({{ $curso->idCurso }})"
                                                class="text-{{ $curso->publicado ? 'green' : 'gray' }}-600 hover:text-{{ $curso->publicado ? 'green' : 'gray' }}-800"
                                                title="{{ $curso->publicado ? 'Ocultar' : 'Publicar' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <a href="{{ route('admin.cursos.edit', $curso->idCurso) }}"
                                           class="text-blue-600 hover:text-blue-800"
                                           title="Editar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay cursos con estudiantes inscritos.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Cursos Recientes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800">Cursos Recientes</h2>
                    <a href="{{ route('admin.cursos.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        Ver todos
                    </a>
                </div>
                <div class="p-6">
                    @if($cursosRecientes && $cursosRecientes->count() > 0)
                        <div class="space-y-4">
                            @foreach($cursosRecientes as $curso)
                                <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex items-center space-x-3">
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
                                            <h3 class="font-medium text-gray-900 text-sm">{{ Str::limit($curso->nombre, 25) }}</h3>
                                            <p class="text-xs text-gray-500">
                                                {{ $curso->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                            {{ $curso->publicado ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $curso->publicado ? 'Publicado' : 'Oculto' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay cursos recientes.</p>
                    @endif
                </div>
            </div>

            <!-- Solicitudes Pendientes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Solicitudes Pendientes</h2>
                </div>
                <div class="p-6">
                    @if($solicitudesPendientes && $solicitudesPendientes->count() > 0)
                        <div class="space-y-4">
                            @foreach($solicitudesPendientes as $solicitud)
                                <div class="p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="font-medium text-gray-900 text-sm">{{ $solicitud->asunto }}</h3>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($solicitud->tipo) }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 mb-2 line-clamp-2">{{ $solicitud->mensaje }}</p>
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500">
                                            {{ $solicitud->created_at->diffForHumans() }}
                                        </span>
                                        <button wire:click="marcarSolicitudResuelta({{ $solicitud->idSolicitud }})"
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-xs font-medium">
                                            Resolver
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay solicitudes pendientes.</p>
                    @endif
                </div>
            </div>

            <!-- Estudiantes Recientes -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Estudiantes Recientes</h2>
                </div>
                <div class="p-6">
                    @if($estudiantesRecientes && $estudiantesRecientes->count() > 0)
                        <div class="space-y-4">
                            @foreach($estudiantesRecientes as $estudiante)
                                <div class="flex items-center space-x-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($estudiante->user->name, 0, 1) }}{{ substr($estudiante->user->apellido, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $estudiante->user->name }} {{ $estudiante->user->apellido }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">{{ $estudiante->user->email }}</p>
                                    </div>
                                    <span class="text-xs text-gray-500">
                                        {{ $estudiante->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-4">No hay estudiantes recientes.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
