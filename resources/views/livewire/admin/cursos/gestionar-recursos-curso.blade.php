<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-lg shadow-lg p-8 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-blue-200 text-sm font-semibold uppercase tracking-wide">Material descargable</p>
                    <h1 class="text-3xl font-bold mt-1 text-white">{{ $curso->nombre }}</h1>
                    <p class="text-blue-100 mt-2">
                        Administra plantillas, guías y recursos que verán los estudiantes inscritos.
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('admin.cursos.show', $curso->codigo) }}"
                       class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition">
                        Volver al curso
                    </a>
                    <button wire:click="abrirModal"
                            class="inline-flex items-center px-4 py-2 bg-white text-blue-900 hover:bg-blue-50 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Nuevo recurso
                    </button>
                </div>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col md:flex-row md:items-center gap-4">
                <div class="flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search"
                           class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Buscar por título o descripción...">
                </div>
                <select wire:model.live="filtroTipo"
                        class="rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                    <option value="todos">Todos los tipos</option>
                    <option value="plantilla">Plantillas</option>
                    <option value="guia">Guías</option>
                    <option value="recurso">Recursos</option>
                </select>
            </div>
        </div>

        {{-- Listado --}}
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <table class="min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Recurso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Versión</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-slate-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-200">
                    @forelse ($this->recursos as $recurso)
                        <tr wire:key="recurso-{{ $recurso->id }}">
                            <td class="px-6 py-4">
                                <p class="text-sm font-medium text-slate-900">{{ $recurso->titulo }}</p>
                                @if ($recurso->descripcion)
                                    <p class="text-sm text-slate-500 line-clamp-2">{{ $recurso->descripcion }}</p>
                                @endif
                                <div class="flex gap-3 mt-1 text-xs text-slate-400">
                                    @if ($recurso->archivo_path)
                                        <span>Archivo adjunto</span>
                                    @endif
                                    @if ($recurso->enlace)
                                        <a href="{{ $recurso->enlace }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">Enlace externo</a>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $recurso->tipo === 'plantilla' ? 'bg-purple-100 text-purple-800' : ($recurso->tipo === 'guia' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $recurso->tipo_etiqueta }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">v{{ $recurso->version }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button wire:click="togglePublicado({{ $recurso->id }})"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $recurso->publicado ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                    {{ $recurso->publicado ? 'Publicado' : 'Oculto' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button wire:click="editarRecurso({{ $recurso->id }})"
                                        class="text-blue-600 hover:text-blue-800 mr-3">Editar</button>
                                <button wire:click="eliminarRecurso({{ $recurso->id }})"
                                        wire:confirm="¿Eliminar este recurso?"
                                        class="text-red-600 hover:text-red-800">Eliminar</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No hay recursos registrados para este curso.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $this->recursos->links() }}</div>
    </div>

    {{-- Modal --}}
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[90vh] overflow-y-auto">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">
                        {{ $recursoEditandoId ? 'Editar recurso' : 'Nuevo recurso' }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
                        <input type="text" wire:model="titulo"
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('titulo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Descripción</label>
                        <textarea wire:model="descripcion" rows="3"
                                  class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"></textarea>
                        @error('descripcion') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Tipo</label>
                            <select wire:model="tipo"
                                    class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                                <option value="plantilla">Plantilla</option>
                                <option value="guia">Guía</option>
                                <option value="recurso">Recurso</option>
                            </select>
                            @error('tipo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Versión</label>
                            <input type="text" wire:model="version"
                                   class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                            @error('version') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Enlace externo (opcional)</label>
                        <input type="url" wire:model="enlace"
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="https://...">
                        @error('enlace') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Archivo (opcional, máx. 20 MB)</label>
                        <input type="file" wire:model="archivo"
                               class="w-full text-sm text-slate-600">
                        <div wire:loading wire:target="archivo" class="text-xs text-slate-500 mt-1">Subiendo...</div>
                        @error('archivo') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" wire:model="publicado"
                               class="rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                        <span class="text-sm text-slate-700">Visible para los estudiantes</span>
                    </label>
                </div>
                <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 rounded-b-lg">
                    <button wire:click="cerrarModal"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">Cancelar</button>
                    <button wire:click="guardar"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
