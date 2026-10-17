<div>
    <div class="w-full max-w-[1400px] mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Lista de Asistencia</h1>
                <p class="text-gray-600 mt-1">{{ $curso->nombre }}</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button type="button" wire:click="exportarExcel"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center px-3 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium transition-colors disabled:opacity-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span wire:loading.remove wire:target="exportarExcel">Exportar Excel</span>
                    <span wire:loading wire:target="exportarExcel">Exportando...</span>
                </button>
                <a href="{{ url()->previous() }}"
                   class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Controles -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date" wire:model.live="fecha"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="md:col-span-2 flex flex-wrap gap-2">
                    <button type="button" wire:click="marcarTodos('presente')"
                            class="px-3 py-2 rounded-lg bg-green-600 hover:bg-green-700 text-white text-sm font-medium transition-colors">
                        Todos presentes
                    </button>
                    <button type="button" wire:click="marcarTodos('ausente')"
                            class="px-3 py-2 rounded-lg bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition-colors">
                        Todos ausentes
                    </button>
                </div>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                <p class="text-xs font-medium text-green-700">Presentes</p>
                <p class="text-xl font-bold text-green-800">{{ $resumen['presente'] }}</p>
            </div>
            <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                <p class="text-xs font-medium text-red-700">Ausentes</p>
                <p class="text-xl font-bold text-red-800">{{ $resumen['ausente'] }}</p>
            </div>
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-center">
                <p class="text-xs font-medium text-amber-700">Tarde</p>
                <p class="text-xl font-bold text-amber-800">{{ $resumen['tarde'] }}</p>
            </div>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-center">
                <p class="text-xs font-medium text-blue-700">Justificados</p>
                <p class="text-xl font-bold text-blue-800">{{ $resumen['justificado'] }}</p>
            </div>
        </div>

        <!-- Tabla -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($estudiantes->isEmpty())
                <p class="text-gray-500 text-center py-8">Este curso no tiene estudiantes inscritos.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Observación</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($estudiantes as $estudiante)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $estudiante->user?->name }} {{ $estudiante->user?->apellido }}
                                        </div>
                                        <div class="text-xs text-gray-500">Código: {{ $estudiante->codigo }}</div>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <select wire:model="estados.{{ $estudiante->codigo }}"
                                                class="border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <option value="presente">Presente</option>
                                            <option value="ausente">Ausente</option>
                                            <option value="tarde">Tarde</option>
                                            <option value="justificado">Justificado</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <input type="text" wire:model="observaciones.{{ $estudiante->codigo }}"
                                               placeholder="Opcional"
                                               class="w-full border border-gray-300 rounded-lg px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                    <button type="button" wire:click="guardar"
                            wire:loading.attr="disabled"
                            class="px-6 py-2 rounded-lg bg-blue-600 hover:bg-blue-700 text-white font-medium transition-colors disabled:opacity-50">
                        <span wire:loading.remove wire:target="guardar">Guardar Asistencia</span>
                        <span wire:loading wire:target="guardar">Guardando...</span>
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>
