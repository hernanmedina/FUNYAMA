<div>
    <div class="container mx-auto py-6 px-4">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Eventos</h1>
                <p class="text-gray-600 mt-2">Administra los eventos de la fundación</p>
            </div>
            <a href="{{ route('admin.eventos.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Evento
            </a>
        </div>

        @if (session()->has('success') || session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('success') ?? session('message') }}
            </div>
        @endif

        <!-- Resumen de métricas -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <a href="{{ route('admin.eventos.index') }}"
               class="bg-white rounded-lg shadow p-4 flex items-center hover:shadow-md hover:bg-blue-50 transition-all cursor-pointer"
               title="Ver todos los eventos">
                <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de eventos</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalEventos }}</p>
                </div>
            </a>
            <a href="{{ route('admin.eventos.inscritos') }}"
               class="bg-white rounded-lg shadow p-4 flex items-center hover:shadow-md hover:bg-green-50 transition-all cursor-pointer"
               title="Ver personas inscritas">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Personas inscritas</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalInscritos }}</p>
                </div>
            </a>
            <a href="{{ route('admin.eventos.ingresos') }}"
               class="bg-white rounded-lg shadow p-4 flex items-center hover:shadow-md hover:bg-amber-50 transition-all cursor-pointer"
               title="Ver ingresos por eventos">
                <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total ingresos por eventos</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalIngresos, 2) }}</p>
                </div>
            </a>
            <a href="{{ route('admin.eventos.pagos-pendientes') }}"
               class="bg-white rounded-lg shadow p-4 flex items-center hover:shadow-md hover:bg-red-50 transition-all cursor-pointer"
               title="Ver pagos pendientes de eventos">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pagos pendientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pagosPendientes }}</p>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-lg shadow">
            <!-- Búsqueda y Filtros -->
            <div class="p-6 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar evento</label>
                        <input wire:model.live="search" 
                               type="text" 
                               placeholder="Título, descripción o ubicación..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select wire:model.live="filtroEstado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="publicado">Publicados</option>
                            <option value="no_publicado">No publicados</option>
                            <option value="destacado">Destacados</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabla de eventos -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Evento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Inscritos</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($eventos as $evento)
                            <tr class="hover:bg-gray-50 cursor-pointer"
                                onclick="window.location='{{ route('admin.eventos.show', $evento->idEvento) }}'">
                                <td class="px-6 py-4">
                                    <div>
                                        <h3 class="font-medium text-gray-900">{{ $evento->titulo }}</h3>
                                        <p class="text-sm text-gray-600">{{ Str::limit($evento->descripcion, 250) }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        <p class="font-medium text-gray-900">{{ $evento->fecha->format('d/m/Y') }}</p>
                                        <p class="text-gray-600">{{ $evento->hora_inicio }} - {{ $evento->hora_fin }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        {{ $evento->tipo_evento === 'presencial' ? 'bg-blue-100 text-blue-800' : 
                                           ($evento->tipo_evento === 'virtual' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($evento->tipo_evento) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-600">
                                        {{ $evento->inscritos_actual ?? 0 }}
                                        @if($evento->cupo_maximo)
                                            / {{ $evento->cupo_maximo }}
                                        @endif
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button wire:click="togglePublicado({{ $evento->idEvento }})"
                                                onclick="event.stopPropagation()"
                                                class="text-{{ $evento->publicado ? 'green' : 'gray' }}-600 hover:text-{{ $evento->publicado ? 'green' : 'gray' }}-800"
                                                title="{{ $evento->publicado ? 'Ocultar' : 'Publicar' }}">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </button>
                                        <button wire:click="toggleDestacado({{ $evento->idEvento }})"
                                                onclick="event.stopPropagation()"
                                                class="text-{{ $evento->destacado ? 'yellow' : 'gray' }}-600 hover:text-{{ $evento->destacado ? 'yellow' : 'gray' }}-800"
                                                title="{{ $evento->destacado ? 'Sin destacar' : 'Destacar' }}">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    <div class="flex flex-wrap gap-3">
                                        <a href="{{ route('admin.eventos.show', $evento->idEvento) }}"
                                           onclick="event.stopPropagation()"
                                           class="text-gray-600 hover:text-gray-800 font-medium whitespace-nowrap">
                                            Ver
                                        </a>
                                        <a href="{{ route('admin.eventos.inscripciones', $evento->idEvento) }}"
                                           onclick="event.stopPropagation()"
                                           class="text-purple-600 hover:text-purple-800 font-medium whitespace-nowrap">
                                            Inscripciones
                                        </a>
                                        <a href="{{ route('admin.eventos.edit', $evento->idEvento) }}"
                                           onclick="event.stopPropagation()"
                                           class="text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap">
                                            Editar
                                        </a>
                                        <button wire:click="$dispatch('confirmarEliminar', {id: {{ $evento->idEvento }}})"
                                                onclick="event.stopPropagation()"
                                                class="text-red-600 hover:text-red-800 font-medium whitespace-nowrap">
                                            Eliminar
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    No hay eventos registrados
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $eventos->links() }}
            </div>
        </div>
    </div>

    @script
    <script>
        $wire.on('confirmarEliminar', (data) => {
            if (confirm('¿Estás seguro de que deseas eliminar este evento?')) {
                $wire.eliminarEvento(data.id);
            }
        });
    </script>
    @endscript
</div>
