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
                <h1 class="text-3xl font-bold text-gray-800">Personas Inscritas en Eventos</h1>
                <p class="text-gray-600 mt-2">Inscripciones confirmadas en los eventos de la fundación</p>
            </div>
        </div>

        <!-- Resumen -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de inscritos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $inscripciones->total() }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Eventos con inscritos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $eventosConInscritos->count() }}</p>
                </div>
            </div>
        </div>

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
                        <label class="block text-sm font-medium text-gray-700 mb-2">Evento</label>
                        <select wire:model.live="filtroEvento" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos los eventos</option>
                            @foreach($eventosConInscritos as $evento)
                                <option value="{{ $evento->idEvento }}">{{ $evento->titulo }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado de pago</label>
                        <select wire:model.live="filtroPago" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="pagado">Pagados</option>
                            <option value="pendiente">Pendientes</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de inscritos -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Inscrito</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Evento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha inscripción</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-700 uppercase">Pago</th>
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
                                    {{ $inscripcion->created_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($inscripcion->pago_realizado)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Pagado
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            Pendiente
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <p class="text-gray-500">No hay inscripciones confirmadas para los eventos.</p>
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
