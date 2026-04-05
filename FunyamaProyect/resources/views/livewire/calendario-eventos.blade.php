<div class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-900 mb-2">Calendario de Eventos</h2>
            <p class="text-lg text-gray-600">Descubre los próximos eventos de la fundación</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Vista de Calendario -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="space-y-4">
                        @forelse($eventos as $evento)
                            <div wire:click="seleccionarEvento({{ $evento->idEvento }})" 
                                 class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow cursor-pointer hover:bg-blue-50">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-3 mb-2">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                                {{ $evento['tipo_evento'] === 'presencial' ? 'bg-blue-100 text-blue-800' : 
                                                   ($evento['tipo_evento'] === 'virtual' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                                {{ ucfirst($evento['tipo_evento']) }}
                                            </span>
                                            @if($evento['destacado'])
                                                <span class="inline-flex items-center gap-1 text-yellow-500">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                    <span class="text-xs font-medium">Destacado</span>
                                                </span>
                                            @endif
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $evento['titulo'] }}</h3>
                                        <p class="text-sm text-gray-600 mb-3">{{ Str::limit($evento['descripcion'], 100) }}</p>
                                        <div class="space-y-1 text-sm text-gray-600">
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                </svg>
                                                <span>{{ \Carbon\Carbon::parse($evento['fecha'])->format('d \\d\\e F \\d\\e Y') }}</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                                <span>{{ $evento['hora_inicio'] }} - {{ $evento['hora_fin'] }}</span>
                                            </div>
                                            @if($evento['ubicacion'])
                                                <div class="flex items-center gap-2">
                                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    </svg>
                                                    <span>{{ $evento['ubicacion'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    @if($evento['imagen'])
                                        <div class="ml-4 flex-shrink-0">
                                            <img src="{{ asset('storage/' . $evento['imagen']) }}" 
                                                 alt="{{ $evento['titulo'] }}"
                                                 class="w-32 h-32 rounded-lg object-cover">
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <p class="text-gray-500 text-lg">No hay eventos próximos</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Sidebar - Resumen -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-lg p-6 sticky top-20">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Próximos Eventos</h3>
                    <div class="space-y-3">
                        @forelse($eventos->take(5) as $evento)
                            <div class="p-3 bg-gray-50 rounded-lg hover:bg-blue-50 transition-colors cursor-pointer"
                                 wire:click="seleccionarEvento({{ $evento->idEvento }})">
                                <p class="font-medium text-sm text-gray-900">{{ Str::limit($evento->titulo, 25) }}</p>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $evento->fecha->format('d M Y') }}
                                </p>
                                @if($evento->costo > 0)
                                    <p class="text-xs font-semibold text-blue-600 mt-1">${{ number_format($evento->costo, 2) }}</p>
                                @else
                                    <p class="text-xs font-semibold text-green-600 mt-1">Gratuito</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No hay eventos próximos</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Detalles del Evento -->
        @if($eventoSeleccionado)
            <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
                <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                    <!-- Header del Modal -->
                    <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                        <h2 class="text-2xl font-bold text-gray-900">{{ $eventoSeleccionado->titulo }}</h2>
                        <button wire:click="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Contenido del Modal -->
                    <div class="p-6 space-y-6">
                        @if($eventoSeleccionado->imagen)
                            <img src="{{ asset('storage/' . $eventoSeleccionado->imagen) }}" 
                                 alt="{{ $eventoSeleccionado->titulo }}"
                                 class="w-full h-64 rounded-lg object-cover">
                        @endif

                        <!-- Información Básica -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Fecha</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $eventoSeleccionado->fecha->format('d \\d\\e F \\d\\e Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Hora</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ $eventoSeleccionado->hora_inicio }} - {{ $eventoSeleccionado->hora_fin }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Tipo</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    {{ ucfirst($eventoSeleccionado->tipo_evento) }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-600">Costo</p>
                                <p class="text-lg font-semibold text-gray-900">
                                    @if($eventoSeleccionado->costo > 0)
                                        ${{ number_format($eventoSeleccionado->costo, 2) }}
                                    @else
                                        Gratuito
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Descripción -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Descripción</h3>
                            <p class="text-gray-700">{{ $eventoSeleccionado->descripcion }}</p>
                        </div>

                        <!-- Contenido Detallado -->
                        @if($eventoSeleccionado->contenido)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Detalles</h3>
                                <div class="prose max-w-none text-gray-700">
                                    {!! nl2br(e($eventoSeleccionado->contenido)) !!}
                                </div>
                            </div>
                        @endif

                        <!-- Ubicación -->
                        @if($eventoSeleccionado->ubicacion)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Ubicación</h3>
                                <div class="space-y-2">
                                    @if($eventoSeleccionado->ubicacion)
                                        <p class="flex items-center gap-2 text-gray-700">
                                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            {{ $eventoSeleccionado->ubicacion }}
                                        </p>
                                    @endif
                                    @if($eventoSeleccionado->direccion)
                                        <p class="text-gray-600 text-sm">{{ $eventoSeleccionado->direccion }}</p>
                                    @endif
                                    @if($eventoSeleccionado->ciudad)
                                        <p class="text-gray-600 text-sm">{{ $eventoSeleccionado->ciudad }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Enlace Virtual -->
                        @if($eventoSeleccionado->enlace_virtual)
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">Acceso Virtual</h3>
                                <a href="{{ $eventoSeleccionado->enlace_virtual }}" target="_blank" 
                                   class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                                    </svg>
                                    Acceder a la transmisión
                                </a>
                            </div>
                        @endif

                        <!-- Cupo -->
                        @if($eventoSeleccionado->cupo_maximo)
                            <div>
                                <p class="text-sm font-medium text-gray-600 mb-2">Disponibilidad</p>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" 
                                         style="width: {{ ($eventoSeleccionado->inscritos_actual / $eventoSeleccionado->cupo_maximo * 100) }}%"></div>
                                </div>
                                <p class="text-sm text-gray-600 mt-2">
                                    {{ $eventoSeleccionado->inscritos_actual }}/{{ $eventoSeleccionado->cupo_maximo }} participantes
                                </p>
                            </div>
                        @endif
                    </div>

                    <!-- Footer del Modal -->
                    <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                        <button wire:click="cerrarModal()" 
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
