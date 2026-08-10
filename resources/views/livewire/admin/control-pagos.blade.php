<div>
    <div class="w-full max-w-[1600px] mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Control de Pagos</h1>
                <p class="text-gray-600 mt-2">Gestiona el estado de pago de todas las matriculas</p>
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
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Matriculas</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totales->total }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Pendientes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totales->pendientes }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Parciales</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totales->parciales }}</p>
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
                        <p class="text-sm font-medium text-gray-600">Completos</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totales->completos }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y Busqueda -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Nombre del curso o estudiante..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-transparent"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por estado</label>
                    <div class="flex items-center gap-2">
                        @foreach(['pendiente' => 'Pendiente', 'parcial' => 'Parcial', 'completo' => 'Completo'] as $valor => $etiqueta)
                            <button
                                wire:click="setEstadoPagoFilter('{{ $valor }}')"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                                    {{ $estadoPagoFilter === $valor ? 'bg-amber-500 text-white shadow' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                                {{ $etiqueta }}
                            </button>
                        @endforeach
                        @if($estadoPagoFilter)
                            <button
                                wire:click="setEstadoPagoFilter(null)"
                                class="px-2 py-2 rounded-lg text-sm text-red-500 hover:bg-red-50 transition-colors">
                                &#10005;
                            </button>
                        @endif
                    </div>
                </div>
            </div>

        <!-- Tabla de Matriculas -->
        @if($matriculas->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Monto Pagado</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscripcion</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($matriculas as $matricula)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $matricula->curso_nombre }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-8 w-8 rounded-full bg-amber-100 flex items-center justify-center">
                                                <span class="text-amber-600 font-semibold text-xs">
                                                    {{ strtoupper(substr($matricula->name, 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900">{{ $matricula->name }} {{ $matricula->apellido }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-medium text-gray-900">${{ number_format((float) $matricula->pago_realizado, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <select
                                            wire:change="actualizarEstadoPago('{{ $matricula->curso_id }}', '{{ $matricula->estudiante_id }}', $event.target.value)"
                                            class="border rounded-lg px-3 py-1.5 text-sm font-medium focus:outline-none focus:ring-2 focus:ring-amber-500
                                            {{ match($matricula->estado_pago) {
                                                'pendiente' => 'border-red-300 bg-red-50 text-red-700',
                                                'parcial' => 'border-yellow-300 bg-yellow-50 text-yellow-700',
                                                'completo' => 'border-green-300 bg-green-50 text-green-700',
                                                default => 'border-gray-300 bg-white text-gray-700'
                                            } }}"
                                        >
                                            <option value="pendiente" @selected($matricula->estado_pago === 'pendiente')>Pendiente</option>
                                            <option value="parcial" @selected($matricula->estado_pago === 'parcial')>Parcial</option>
                                            <option value="completo" @selected($matricula->estado_pago === 'completo')>Completo</option>
                                        </select>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($matricula->fecha_inscripcion)->format('d/m/Y') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $matriculas->links() }}
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <svg class="mx-auto h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                </svg>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    @if($search || $estadoPagoFilter) Sin resultados @else Sin matriculas @endif
                </h3>
                <p class="text-gray-600">
                    @if($search || $estadoPagoFilter)
                        No se encontraron matriculas con los filtros actuales.
                    @else
                        No hay matriculas registradas en el sistema.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>
        </div>