<div>
    <div class="w-full max-w-[1600px] mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Opiniones de Estudiantes</h1>
                <p class="text-gray-600 mt-2">Todas las valoraciones y comentarios de los estudiantes sobre los cursos</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Estadisticas -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Opiniones</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalOpiniones }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Rating Promedio</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $promedioRating }} <span class="text-yellow-400 text-lg">★</span></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Por pagina</p>
                        <p class="text-2xl font-bold text-gray-900">15</p>
                        <p class="text-xs text-gray-500">opiniones</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Busqueda -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Curso, estudiante o comentario..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    />
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por puntuacion</label>
                    <div class="flex items-center gap-1">
                        @for ($i = 5; $i >= 1; $i--)
                            <button
                                wire:click="setRatingFilter({{ $i }})"
                                class="px-3 py-2 rounded-lg text-sm font-medium transition-colors
                                    {{ $ratingFilter === $i ? 'bg-yellow-400 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $i }} ★
                            </button>
                        @endfor
                        @if($ratingFilter)
                            <button
                                wire:click="setRatingFilter(null)"
                                class="px-2 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 transition-colors">
                                ✕
                            </button>
                        @endif
                    </div>
                </div>

                <div>
                    <label for="sortBy" class="block text-sm font-medium text-gray-700 mb-2">Ordenar por</label>
                    <select
                        wire:model.live="sortBy"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                    >
                        <option value="fecha">Mas recientes</option>
                        <option value="rating">Mejor puntuadas</option>
                        <option value="curso">Nombre del curso</option>
                        <option value="estudiante">Nombre del estudiante</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Opiniones -->
        @if($opiniones->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Puntuacion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Opinion</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($opiniones as $opinion)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                                <span class="text-purple-600 font-semibold text-sm">
                                                    {{ strtoupper(substr($opinion->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $opinion->name }} {{ $opinion->apellido }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $opinion->curso_nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <span class="text-lg {{ $opinion->rating_estudiante >= $i ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                                            @endfor
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($opinion->opinion_estudiante)
                                            <p class="text-sm text-gray-700 italic max-w-md line-clamp-2">
                                                "{{ $opinion->opinion_estudiante }}"
                                            </p>
                                        @else
                                            <span class="text-sm text-gray-400">Sin comentario</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($opinion->updated_at)->diffForHumans() }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $opiniones->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    @if($search || $ratingFilter)
                        Sin resultados
                    @else
                        Sin opiniones aun
                    @endif
                </h3>
                <p class="text-gray-600">
                    @if($search || $ratingFilter)
                        No se encontraron opiniones con los filtros actuales. Intenta con otros criterios.
                    @else
                        Los estudiantes aun no han dejado opiniones sobre los cursos.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
