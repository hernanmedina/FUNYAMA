<div>
    <div class="w-full max-w-[1600px] mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Calificar Estudiantes</h1>
                <p class="text-gray-600 mt-2">Asigna calificaciones y consulta las notas de los estudiantes</p>
            </div>
            <a href="{{ route('admin.dashboard') }}"
               class="inline-flex items-center px-4 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al Dashboard
            </a>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Calificados</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats->total_calificados ?? 0 }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Promedio General</p>
                        <p class="text-2xl font-bold text-gray-900">{{ number_format($stats->promedio ?? 0, 1) }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-emerald-100 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Excelentes (9-10)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats->excelentes ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Insuficientes (&lt;5)</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats->insuficientes ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            <!-- Formulario de Calificación -->
            <div class="lg:col-span-1 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-800">Asignar Calificación</h2>
                    <p class="text-sm text-gray-500">Selecciona curso y estudiante para calificar.</p>
                </div>
                <div class="p-6 space-y-5">
                    {{-- Seleccionar Curso --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Curso</label>
                        <select wire:model.live="cursoCalificarId"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <option value="">-- Selecciona un curso --</option>
                            @foreach($cursosConEstudiantes as $curso)
                                <option value="{{ $curso['codigo'] }}">{{ $curso['nombre'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Seleccionar Estudiante --}}
                    @if($cursoCalificarId)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estudiante</label>
                            <select wire:model.live="estudianteCalificarId"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">-- Selecciona un estudiante --</option>
                                @foreach($estudiantesParaCalificar as $est)
                                    <option value="{{ $est['estudiante_id'] }}">
                                        {{ $est['nombre_completo'] }}
                                        ({{ $est['estado'] === 'completado' ? 'Completado' : 'En progreso' }})
                                        @if($est['calificacion_actual'] !== null)
                                            — Nota actual: {{ $est['calificacion_actual'] }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if(empty($estudiantesParaCalificar))
                                <p class="text-sm text-gray-500 mt-1">No hay estudiantes completados/en progreso en este curso.</p>
                            @endif
                        </div>
                    @endif

                    {{-- Calificación y Retroalimentación --}}
                    @if($estudianteCalificarId)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Calificación (0 - 10)
                            </label>
                            <input type="number"
                                   wire:model="notaCalificacion"
                                   step="0.1"
                                   min="0"
                                   max="10"
                                   placeholder="Ej: 8.5"
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
                            @error('notaCalificacion')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Retroalimentación
                            </label>
                            <textarea wire:model="retroalimentacion"
                                      rows="4"
                                      maxlength="1000"
                                      placeholder="Escribe comentarios sobre el desempeño del estudiante..."
                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
                            <p class="text-xs text-gray-500 mt-1">{{ strlen($retroalimentacion) }}/1000 caracteres</p>
                        </div>

                        <button type="button"
                                wire:click="guardarCalificacion"
                                wire:loading.attr="disabled"
                                class="w-full px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed font-medium">
                            <span wire:loading.remove>Guardar Calificación</span>
                            <span wire:loading>
                                <svg class="animate-spin h-4 w-4 inline" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    @endif
                </div>
            </div>

            <!-- Lista de Estudiantes Calificados -->
            <div class="lg:col-span-2 bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Estudiantes Calificados</h2>
                        <p class="text-sm text-gray-500">Consulta las notas asignadas a los estudiantes.</p>
                    </div>
                    <div class="flex gap-2">
                        <button type="button"
                                wire:click="exportarExcel"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-3 py-2 rounded-lg bg-green-600 text-white text-sm font-medium hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span wire:loading.remove wire:target="exportarExcel">Excel</span>
                            <span wire:loading wire:target="exportarExcel">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                        <button type="button"
                                wire:click="exportarCsv"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center px-3 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                            </svg>
                            <span wire:loading.remove wire:target="exportarCsv">CSV</span>
                            <span wire:loading wire:target="exportarCsv">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Buscar</label>
                            <input type="text"
                                   wire:model.live.debounce.300ms="search"
                                   placeholder="Estudiante, curso o código..."
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Curso</label>
                            <select wire:model.live="cursoFiltro"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Todos los cursos</option>
                                @foreach($cursosParaFiltro as $curso)
                                    <option value="{{ $curso['codigo'] }}">{{ $curso['nombre'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Estado</label>
                            <select wire:model.live="estadoFiltro"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Todos</option>
                                <option value="completado">Completado</option>
                                <option value="en_progreso">En progreso</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Rango de Nota</label>
                            <select wire:model.live="rangoNota"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                <option value="">Todas</option>
                                <option value="excelente">Excelente (9 - 10)</option>
                                <option value="bueno">Bueno (7 - 8.9)</option>
                                <option value="regular">Regular (5 - 6.9)</option>
                                <option value="insuficiente">Insuficiente (&lt; 5)</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="button"
                                wire:click="limpiarFiltros"
                                class="inline-flex items-center px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Limpiar filtros
                        </button>
                    </div>
                </div>

                <!-- Tabla -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nota</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Retroalimentación</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($calificaciones as $cal)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="ml-0">
                                                <div class="text-sm font-medium text-gray-900">{{ $cal->name }} {{ $cal->apellido }}</div>
                                                <div class="text-xs text-gray-500">{{ $cal->estudiante_codigo }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $cal->curso_nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($cal->estado === 'completado')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Completado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                En progreso
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $nota = (float) $cal->calificacion;
                                            $color = $nota >= 9 ? 'text-green-600' : ($nota >= 7 ? 'text-blue-600' : ($nota >= 5 ? 'text-yellow-600' : 'text-red-600'));
                                        @endphp
                                        <span class="text-sm font-bold {{ $color }}">{{ number_format($nota, 1) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-sm text-gray-600 max-w-xs truncate" title="{{ $cal->comentario_calificacion }}">
                                            {{ $cal->comentario_calificacion ?: '—' }}
                                        </p>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <p class="text-gray-500 font-medium">No hay estudiantes calificados</p>
                                        <p class="text-gray-400 text-sm mt-1">Asigna calificaciones usando el formulario de la izquierda.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Paginación -->
                @if($calificaciones->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $calificaciones->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
