<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-slate-900 mb-2">Mis Eventos</h1>
            <p class="text-slate-600">Visualiza los eventos a los que te has inscrito</p>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 mb-6">
            <button
                wire:click="$set('tab', 'proximos')"
                class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 {{ $tab === 'proximos' ? 'bg-blue-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 shadow-sm' }}">
                📅 Próximos Eventos
            </button>
            <button
                wire:click="$set('tab', 'pasados')"
                class="px-6 py-3 rounded-lg font-medium text-sm transition-all duration-200 {{ $tab === 'pasados' ? 'bg-green-600 text-white shadow-md' : 'bg-white text-slate-600 hover:bg-slate-100 shadow-sm' }}">
                ✅ Eventos Pasados
            </button>
        </div>

        <!-- Búsqueda -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="max-w-md">
                <label for="search" class="block text-sm font-medium text-slate-700 mb-2">
                    Buscar eventos
                </label>
                <input
                    wire:model.live="search"
                    type="text"
                    placeholder="Nombre del evento..."
                    class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                />
            </div>
        </div>

        <!-- Grid de Eventos -->
        @if($this->misInscripciones->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($this->misInscripciones as $inscripcion)
                    @php($evento = $inscripcion->evento)
                    <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow overflow-hidden flex flex-col">
                        <!-- Imagen del evento -->
                        @if($evento && $evento->imagen)
                            <div class="h-40 overflow-hidden">
                                <img src="{{ asset('storage/' . $evento->imagen) }}"
                                     alt="{{ $evento->titulo }}"
                                     class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="h-40 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <svg class="w-16 h-16 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif

                        <!-- Contenido -->
                        <div class="p-6 flex flex-col flex-1">
                            <div class="flex items-center gap-2 mb-2">
                                @if($evento)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                        {{ $evento->tipo_evento === 'presencial' ? 'bg-blue-100 text-blue-800' :
                                           ($evento->tipo_evento === 'virtual' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                        {{ ucfirst($evento->tipo_evento) }}
                                    </span>
                                @endif
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Confirmada
                                </span>
                            </div>

                            <h3 class="text-lg font-bold text-slate-900 mb-2 line-clamp-2">
                                {{ $evento?->titulo ?? 'Evento eliminado' }}
                            </h3>

                            @if($evento)
                                <div class="space-y-2 text-sm text-slate-600 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <span>{{ $evento->fecha->format('d \\d\\e F \\d\\e Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>{{ $evento->hora_inicio }} - {{ $evento->hora_fin }}</span>
                                    </div>
                                    @if($evento->ubicacion)
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <span>{{ $evento->ubicacion }}</span>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-auto pt-4 border-t border-slate-100 flex justify-between items-center">
                                <span class="text-xs text-slate-500">
                                    Inscrito el {{ $inscripcion->created_at->format('d/m/Y') }}
                                </span>
                                @if($evento && $evento->fecha >= now())
                                    <button
                                        wire:click="cancelarInscripcion({{ $inscripcion->idInscripcion }})"
                                        wire:confirm="¿Seguro que deseas cancelar tu inscripción a este evento?"
                                        class="text-sm text-red-600 hover:text-red-800 font-medium">
                                        Cancelar
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Paginación -->
            @if($this->misInscripciones->hasPages())
                <div class="mt-8 flex justify-center">
                    {{ $this->misInscripciones->links() }}
                </div>
            @endif
        @else
            <!-- Estado vacío -->
            <div class="text-center py-16 bg-white rounded-lg shadow-md">
                <svg class="w-20 h-20 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-lg font-semibold text-slate-600 mb-2">
                    No tienes {{ $tab === 'pasados' ? 'eventos pasados' : 'eventos próximos' }}
                </h3>
                <p class="text-slate-400 mb-6">
                    {{ $tab === 'pasados' ? 'Aún no has asistido a ningún evento.' : 'Explora el calendario de eventos e inscríbete.' }}
                </p>
                <a href="{{ route('eventos.index') }}"
                   class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Ver eventos disponibles
                </a>
            </div>
        @endif
    </div>
</div>
