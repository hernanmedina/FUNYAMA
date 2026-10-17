<div>
    <div class="w-full max-w-[1400px] mx-auto py-6 px-4">
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Mis Cursos</h1>
                <p class="text-gray-600 mt-1">Cursos que impartes como docente</p>
            </div>
            <a href="{{ route('docente.dashboard') }}"
               class="inline-flex items-center px-3 py-2 rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver al panel
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <input type="text" wire:model.live.debounce.300ms="search"
                   placeholder="Buscar por nombre o código..."
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        @if($cursos->isEmpty())
            <div class="bg-white rounded-lg shadow p-8 text-center text-gray-500">
                No se encontraron cursos.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($cursos as $curso)
                    <div class="bg-white rounded-lg shadow overflow-hidden flex flex-col">
                        @if($curso->imagen_portada)
                            <img src="{{ Storage::disk('public')->url($curso->imagen_portada) }}"
                                 alt="{{ $curso->nombre }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center">
                                <span class="text-white text-lg font-semibold px-4 text-center">{{ $curso->nombre }}</span>
                            </div>
                        @endif

                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-semibold text-gray-800 mb-1">{{ $curso->nombre }}</h3>
                            <p class="text-sm text-gray-500 mb-3">{{ $curso->estudiantes_count }} estudiantes</p>

                            <div class="mt-auto flex flex-wrap gap-2">
                                <a href="{{ route('docente.cursos.editar', $curso) }}"
                                   class="text-xs px-3 py-1.5 rounded bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                                    Editar
                                </a>
                                <a href="{{ route('docente.cursos.recursos', $curso) }}"
                                   class="text-xs px-3 py-1.5 rounded bg-purple-600 hover:bg-purple-700 text-white transition-colors">
                                    Materiales
                                </a>
                                <a href="{{ route('docente.cursos.asistencias', $curso) }}"
                                   class="text-xs px-3 py-1.5 rounded bg-amber-600 hover:bg-amber-700 text-white transition-colors">
                                    Asistencias
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-6">
                {{ $cursos->links() }}
            </div>
        @endif
    </div>
</div>
