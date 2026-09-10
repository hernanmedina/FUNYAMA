<div>
    <div class="w-full max-w-7xl mx-auto py-10 px-4">
        <!-- Header -->
        <div class="text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Opiniones de Estudiantes</h1>
            <p class="text-gray-600 mt-3 max-w-2xl mx-auto">
                Descubre lo que nuestros estudiantes opinan sobre los cursos de la Fundación YAMA.
            </p>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-10 max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-sm font-medium text-gray-600">Total de opiniones</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">{{ $totalOpiniones }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-sm font-medium text-gray-600">Rating promedio</p>
                <p class="text-3xl font-bold text-gray-900 mt-1">
                    {{ $promedioRating }} <span class="text-yellow-400 text-2xl">★</span>
                </p>
            </div>
        </div>

        <!-- Filtros -->
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por puntuación</label>
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
                        <option value="fecha">Más recientes</option>
                        <option value="rating">Mejor puntuadas</option>
                        <option value="curso">Nombre del curso</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Listado de opiniones -->
        @if($opiniones->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($opiniones as $opinion)
                    <div class="bg-white rounded-lg shadow p-6 flex flex-col">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0 h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="text-purple-600 font-semibold">
                                    {{ strtoupper(substr($opinion->name, 0, 1)) }}
                                </span>
                            </div>
                            <div class="ml-4 min-w-0">
                                <div class="text-sm font-semibold text-gray-900 truncate">
                                    {{ $opinion->name }} {{ $opinion->apellido }}
                                </div>
                                <div class="text-xs text-gray-500 truncate">{{ $opinion->curso_nombre }}</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-0.5 mb-3">
                            @for ($i = 1; $i <= 5; $i++)
                                <span class="text-lg {{ $opinion->rating_estudiante >= $i ? 'text-yellow-400' : 'text-gray-200' }}">★</span>
                            @endfor
                        </div>

                        <p class="text-sm text-gray-700 italic flex-1">
                            "{{ $opinion->opinion_estudiante }}"
                        </p>

                        <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-400">
                            {{ \Carbon\Carbon::parse($opinion->updated_at)->diffForHumans() }}
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $opiniones->links() }}
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
                        Sin opiniones aún
                    @endif
                </h3>
                <p class="text-gray-600">
                    @if($search || $ratingFilter)
                        No se encontraron opiniones con los filtros actuales. Intenta con otros criterios.
                    @else
                        Los estudiantes aún no han dejado opiniones sobre los cursos.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
