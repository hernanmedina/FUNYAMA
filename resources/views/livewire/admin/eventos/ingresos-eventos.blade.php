<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <a href="{{ route('admin.eventos.index') }}" class="text-sm text-blue-600 hover:text-blue-800 mb-2 inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver a eventos
                </a>
                <h1 class="text-3xl font-bold text-gray-800">Ingresos por Eventos</h1>
                <p class="text-gray-600 mt-2">Pagos registrados de las inscripciones confirmadas</p>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
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
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pagos registrados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $inscripciones->total() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow">
            <!-- Búsqueda y Filtros -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar inscrito</label>
                        <input wire:model.live="search"
                               type="text"
                               placeholder="Nombre, email o documento..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Evento</label>
                        <select wire:model.live="filtroEvento" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos los eventos</option>
                            @foreach($eventosConIngresos as $evento)
                                <option value="{{ $evento->idEvento }}">{{ $evento->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de ingresos -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Inscrito</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Evento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha pago</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase">Monto</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inscripciones as $inscripcion)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
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
                                </td>
                                <td class="px-6 py-4">
                                    @if($inscripcion->evento)
                                        <a href="{{ route('admin.eventos.show', $inscripcion->evento->idEvento) }}"
                                           class="text-sm font-medium text-blue-600 hover:text-blue-800">
                                            {{ $inscripcion->evento->titulo }}
                                        </a>
                                        <p class="text-xs text-gray-500">
                                            {{ $inscripcion->evento->fecha->format('d/m/Y') }}
                                        </p>
                                    @else
                                        <span class="text-sm text-gray-400">Evento eliminado</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $inscripcion->email }}</p>
                                    @if($inscripcion->telefono)
                                        <p class="text-sm text-gray-600">{{ $inscripcion->telefono }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $inscripcion->updated_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                    @if($inscripcion->evento && $inscripcion->evento->costo > 0)
                                        ${{ number_format($inscripcion->evento->costo, 2) }}
                                    @else
                                        Gratuito
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                                    </svg>
                                    <p class="text-gray-500">No hay pagos registrados para los eventos.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $inscripciones->links() }}
            </div>
        </div>
    </div>
</div>
