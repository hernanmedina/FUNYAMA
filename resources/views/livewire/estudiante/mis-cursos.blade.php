<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Mis Cursos Inscritos</h1>
            <p class="text-slate-600">Visualiza y gestiona todos los cursos en los que estás inscrito</p>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-slate-700 mb-2">
                        Buscar cursos
                    </label>
                    <input
                        wire:model.live="search"
                        type="text"
                        placeholder="Nombre o descripción..."
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    />
                </div>
                <div>
                    <label for="sortBy" class="block text-sm font-medium text-slate-700 mb-2">
                        Ordenar por
                    </label>
                    <select
                        wire:model.live="sortBy"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="fecha_inscripcion">Fecha de inscripción</option>
                        <option value="nombre">Nombre</option>
                        <option value="codigo">Código</option>
                    </select>
                </div>
                <div>
                    <label for="sortDirection" class="block text-sm font-medium text-slate-700 mb-2">
                        Dirección
                    </label>
                    <select
                        wire:model.live="sortDirection"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                        <option value="desc">Descendente</option>
                        <option value="asc">Ascendente</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Grid de Cursos -->
        @if($this->misCursos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->misCursos as $curso)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden">
                        <!-- Encabezado del curso -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                            <h3 class="text-lg font-bold text-white mb-1">{{ $curso->nombre }}</h3>
                            <p class="text-blue-100 text-sm">{{ $curso->codigo }}</p>
                        </div>

                        <!-- Contenido -->
                        <div class="p-6">
                            <!-- Descripción -->
                            <p class="text-slate-600 text-sm mb-4 line-clamp-2">
                                {{ $curso->descripcion }}
                            </p>

                            <!-- Estado y Progreso -->
                            <div class="mb-4 space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-slate-500">Estado:</span>
                                    <span class="px-2 py-1 rounded-full text-xs font-medium
                                        @if($curso->pivot && $curso->pivot->estado === 'completado')
                                            bg-green-100 text-green-700
                                        @elseif($curso->pivot && $curso->pivot->estado === 'en_progreso')
                                            bg-blue-100 text-blue-700
                                        @else
                                            bg-slate-100 text-slate-700
                                        @endif
                                    ">
                                        {{ $curso->pivot ? ucfirst(str_replace('_', ' ', $curso->pivot->estado)) : 'Estado desconocido' }}
                                    </span>
                                </div>

                                <!-- Barra de Progreso -->
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-slate-600">Progreso</span>
                                        <span class="text-xs font-bold {{ ($curso->pivot ? $curso->pivot->progreso : 0) >= 100 ? 'text-green-600' : 'text-blue-600' }}">
                                            {{ $curso->pivot ? number_format($curso->pivot->progreso, 1) : 0 }}%
                                        </span>
                                    </div>
                                    <div class="w-full bg-slate-200 rounded-full h-2.5">
                                        <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-2.5 rounded-full transition-all duration-500 ease-out"
                                             style="width: {{ $curso->pivot ? $curso->pivot->progreso : 0 }}%">
                                        </div>
                                    </div>
                                    @if($curso->temario_items && count($curso->temario_items) > 0)
                                        @php
                                            $temarioProgreso = $curso->pivot->temario_progreso ?? [];
                                            if (is_string($temarioProgreso)) {
                                                $temarioProgreso = json_decode($temarioProgreso, true) ?? [];
                                            }
                                            $completados = is_array($temarioProgreso) ? count(array_filter($temarioProgreso, fn($v) => $v === true)) : 0;
                                            $totalTemas = count($curso->temario_items);
                                        @endphp
                                        <div class="mt-1 text-right">
                                            <span class="text-xs text-slate-400">
                                                {{ $completados }}/{{ $totalTemas }} temas completados
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Fecha de Inscripción -->
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-medium text-slate-500">Inscrito el:</span>
                                    <span class="text-xs text-slate-600">
                                        {{ $curso->pivot ? \Carbon\Carbon::parse($curso->pivot->fecha_inscripcion)->format('d M Y') : 'Fecha desconocida' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Información del Curso -->
                            <div class="border-t border-slate-200 pt-4 mb-4 space-y-2 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Instructor:</span>
                                    <span class="font-medium text-slate-900">{{ $curso->instructor }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-600">Duración:</span>
                                    <span class="font-medium text-slate-900">{{ $curso->duracion }} horas</span>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('cursos.show', $curso->codigo) }}"
                                   class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                                    Ver Detalles
                                </a>
                                
                                @if($curso->pivot && $curso->pivot->estado === 'completado' && is_null($curso->pivot->calificacion))
                                    <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                        <h4 class="text-sm font-semibold text-yellow-800 mb-2">¡Califica este curso!</h4>
                                        <p class="text-gray-600">Esta es una autoevaluación de tu experiencia en este curso, esta nota será tenida en cuenta según el criterio del docente </p>
                                        <form wire:submit="registrarEvaluacion('{{ $curso->codigo }}')">
                                            <input type="number" wire:model="evaluaciones.{{ $curso->codigo }}.calificacion" min="1" max="5" placeholder="Puntuación (1-5)" class="w-full mb-2 p-2 border rounded" required>
                                            <textarea wire:model="evaluaciones.{{ $curso->codigo }}.comentario" placeholder="Tu comentario..." class="w-full mb-2 p-2 border rounded" required></textarea>
                                            <button type="submit" class="w-full bg-yellow-500 text-white py-2 rounded hover:bg-yellow-600">Enviar</button>
                                        </form>
                                    </div>
                                @elseif($curso->pivot && $curso->pivot->estado === 'completado' && !is_null($curso->pivot->calificacion))
                                    <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                                        <p class="text-sm text-green-800">Has calificado este curso con {{ $curso->pivot->calificacion }}/5</p>
                                    </div>
                                @endif

                                @if($curso->pivot && $curso->pivot->estado !== 'completado')
                                    <button wire:click="marcarComoCompletado('{{ $curso->codigo }}')"
                                            class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                                        Marcar como Finalizado
                                    </button>
                                    @if($curso->enlace_classroom)
                                        <a href="{{ $curso->enlace_classroom }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-500 hover:bg-green-600 text-white font-medium rounded-lg transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                            Ir a Classroom
                                        </a>
                                    @else
                                        <a href="{{ route('cursos.show', $curso->codigo) }}"
                                           class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 font-medium rounded-lg transition-colors">
                                            Continuar Curso
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            <div class="mt-8">
                {{ $this->misCursos->links() }}
            </div>
        @else
            <!-- Estado Vacío -->
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C6.5 6.253 2 10.753 2 16.5S6.5 26.747 12 26.747s10-4.5 10-10.247S17.5 6.253 12 6.253z" />
                </svg>
                <h3 class="text-lg font-semibold text-slate-900 mb-2">No hay cursos inscritos</h3>
                <p class="text-slate-600 mb-6">Aún no te has inscrito en ningún curso. ¡Explora el catálogo de cursos disponibles!</p>
                <a href="{{ route('cursos.index') }}"
                   class="inline-flex items-center px-6 py-2 bg-blue-500 hover:bg-blue-600 text-white font-medium rounded-lg transition-colors">
                    Explorar Cursos
                </a>
            </div>
        @endif
    </div>
</div>
