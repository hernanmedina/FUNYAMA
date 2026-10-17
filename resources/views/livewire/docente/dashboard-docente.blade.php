<div>
    <div class="w-full max-w-[1600px] mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Panel del Docente</h1>
                <p class="text-gray-600 mt-2">Bienvenido/a, {{ Auth::user()->name }} {{ Auth::user()->apellido }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="/"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3V12h4v9h3a1 1 0 001-1V10"/>
                    </svg>
                    Página de inicio
                </a>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                    Docente
                </span>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 self-start">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div class="mt-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600">Mis Cursos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_cursos'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 self-start">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="mt-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600">Estudiantes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_estudiantes'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 self-start">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="mt-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600">Materiales de Apoyo</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['total_recursos'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex flex-col">
                    <div class="p-3 rounded-full bg-amber-100 text-amber-600 self-start">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                    </div>
                    <div class="mt-4 min-w-0">
                        <p class="text-sm font-medium text-gray-600">Asistencias Registradas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $estadisticas['asistencias_registradas'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Acciones Rápidas</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                <a href="{{ route('docente.cursos.index') }}"
                   class="bg-green-600 hover:bg-green-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24">
                    <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span class="text-sm font-medium">Mis Cursos</span>
                </a>

                @if($cursosImpartidos->isNotEmpty())
                    <a href="{{ route('docente.cursos.recursos', $cursosImpartidos->first()) }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span class="text-sm font-medium">Subir Materiales</span>
                    </a>

                    <a href="{{ route('docente.cursos.asistencias', $cursosImpartidos->first()) }}"
                       class="bg-amber-600 hover:bg-amber-700 text-white p-3 rounded-lg flex flex-col items-center justify-center text-center transition-colors h-24">
                        <svg class="w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                        </svg>
                        <span class="text-sm font-medium">Asistencias</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Mis Cursos -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Mis Cursos</h2>

            @if($cursos->isEmpty())
                <p class="text-gray-500 text-center py-8">Aún no tienes cursos asignados.</p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($cursos as $curso)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <h3 class="font-semibold text-gray-800 mb-1">{{ $curso->nombre }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $curso->estudiantes_count }} estudiantes</p>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('docente.cursos.editar', $curso) }}"
                                   class="text-xs px-3 py-1.5 rounded bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                                    Editar
                                </a>
                                <a href="{{ route('docente.cursos.recursos', $curso) }}"
                                   class="text-xs px-3 py-1.5 rounded bg-purple-600 hover:bg-purple-700 text-white transition-colors">
                                    Materiales
                                </a>
                                <a href="{{ route('docente.cursos.asistencias', $curso) }}"
                                   class="text-xs px-3 py-1.5 rounded bg-amber-600 hover:bg-amber-700 text-white transition-colors">
                                    Asistencias
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
