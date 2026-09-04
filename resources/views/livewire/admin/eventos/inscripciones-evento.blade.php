<div>
    <div class="container mx-auto py-6 px-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('admin.eventos.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-2 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a eventos
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Inscripciones del Evento</h1>
                <p class="text-gray-600 mt-2">{{ $evento->titulo }}</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-600">Fecha: <span class="font-medium">{{ $evento->fecha->format('d/m/Y') }}</span></p>
                <p class="text-sm text-gray-600">Inscritos: <span class="font-medium">{{ $evento->inscritos_actual }}</span>
                    @if($evento->cupo_maximo) / {{ $evento->cupo_maximo }} @endif
                </p>
                @if($evento->costo > 0)
                    <p class="text-sm text-gray-600">Costo: <span class="font-medium">${{ number_format($evento->costo, 2) }}</span></p>
                @endif
            </div>
        </div>

        <!-- Resumen de pagos -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total inscritos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $inscripciones->total() }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pagados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pagados }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pendientes }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total recaudado</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalRecaudado, 2) }}</p>
                </div>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-700 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-lg shadow">
            <!-- Búsqueda y Filtros -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar inscrito</label>
                        <input wire:model.live="search"
                               type="text"
                               placeholder="Nombre, email o documento..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select wire:model.live="filtroEstado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="confirmada">Confirmadas</option>
                            <option value="cancelada">Canceladas</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de inscripciones -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Inscrito</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Documento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha inscripción</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase">Pago</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inscripciones as $inscripcion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div>
                                            <p class="font-medium text-gray-900">
                                                {{ $inscripcion->nombre }} {{ $inscripcion->apellido }}
                                            </p>
                                            @if($inscripcion->usuario)
                                                <p class="text-xs text-gray-500">
                                                    {{ $inscripcion->usuario->isEstudiante() ? 'Estudiante' : 'Usuario' }}
                                                </p>
                                            @else
                                                <p class="text-xs text-gray-400">Invitado</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $inscripcion->email }}</p>
                                    @if($inscripcion->telefono)
                                        <p class="text-sm text-gray-600">{{ $inscripcion->telefono }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $inscripcion->documento ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $inscripcion->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($inscripcion->estado === 'confirmada')
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Confirmada
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Cancelada
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($inscripcion->estado === 'confirmada')
                                        <label class="inline-flex items-center cursor-pointer group" title="{{ $inscripcion->pago_realizado ? 'Marcar como pendiente' : 'Marcar como pagado' }}">
                                            <input type="checkbox"
                                                   wire:click="togglePago({{ $inscripcion->idInscripcion }})"
                                                   @checked($inscripcion->pago_realizado)
                                                   class="w-5 h-5 rounded border-gray-300 text-green-600 focus:ring-green-500 cursor-pointer">
                                            <span class="ml-2 text-sm font-medium {{ $inscripcion->pago_realizado ? 'text-green-600' : 'text-red-500' }}">
                                                {{ $inscripcion->pago_realizado ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </label>
                                    @else
                                        <span class="text-sm text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($inscripcion->estado === 'confirmada')
                                        <button wire:click="cancelarInscripcion({{ $inscripcion->idInscripcion }})"
                                                wire:confirm="¿Seguro que deseas cancelar esta inscripción?"
                                                class="text-red-600 hover:text-red-800 font-medium whitespace-nowrap">
                                            Cancelar
                                        </button>
                                    @else
                                        <button wire:click="reactivarInscripcion({{ $inscripcion->idInscripcion }})"
                                                class="text-green-600 hover:text-green-800 font-medium whitespace-nowrap">
                                            Reactivar
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <p class="text-gray-500">No hay inscripciones para este evento.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            @if($inscripciones->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $inscripciones->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
