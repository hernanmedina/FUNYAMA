<div>
    <div class="w-full max-w-[1200px] mx-auto py-6 px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Crear Nuevo Articulo</h1>
                <p class="text-gray-600 mt-2">Publica un nuevo articulo en el blog de la fundacion</p>
            </div>
            <a href="{{ route('admin.blog.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg">
                ← Volver al Blog
            </a>
        </div>

        <form wire:submit.prevent="store" class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Contenido</h2>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Titulo *</label>
                            <input type="text" wire:model="titulo" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Titulo del articulo">
                            @error('titulo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Slug *</label>
                            <input type="text" wire:model="slug" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="slug-del-articulo">
                            @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Resumen *</label>
                            <textarea wire:model="resumen" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Breve resumen del articulo..."></textarea>
                            @error('resumen') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contenido *</label>
                            <textarea wire:model="contenido" rows="12" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 font-mono text-sm" placeholder="Escribe el contenido completo del articulo..."></textarea>
                            @error('contenido') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Configuracion</h2>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Categoria</label>
                            <select wire:model="categoria" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500">
                                <option value="general">General</option>
                                <option value="noticias">Noticias</option>
                                <option value="eventos">Eventos</option>
                                <option value="educacion">Educacion</option>
                                <option value="comunidad">Comunidad</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Imagen de portada</label>
                            <input type="file" wire:model="imagen_portada" class="w-full text-sm" accept="image/*">
                            @error('imagen_portada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Autor</label>
                            <input type="text" wire:model="autor" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Nombre del autor">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Fuente</label>
                            <input type="text" wire:model="fuente" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" placeholder="Fuente original">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tiempo de lectura (min)</label>
                            <input type="number" wire:model="tiempo_lectura" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500" min="1">
                        </div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Etiquetas</h2>
                        <div class="flex gap-2 mb-2">
                            <input type="text" wire:model="nuevaEtiqueta" wire:keydown.enter.prevent="agregarEtiqueta" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 text-sm" placeholder="Agregar etiqueta...">
                            <button type="button" wire:click="agregarEtiqueta" class="px-3 py-2 bg-blue-500 text-white text-sm rounded-lg hover:bg-blue-600">+</button>
                        </div>
                        @if(count($etiquetas) > 0)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($etiquetas as $index => $etiqueta)
                                    <span class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-full">
                                        {{ $etiqueta }}
                                        <button type="button" wire:click="quitarEtiqueta({{ $index }})" class="ml-1 hover:text-red-500">&times;</button>
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h2 class="text-lg font-semibold text-gray-800 mb-4">Estado</h2>
                        <label class="flex items-center mb-2">
                            <input type="checkbox" wire:model="publicado" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Publicar inmediatamente</span>
                        </label>
                        <label class="flex items-center mb-2">
                            <input type="checkbox" wire:model="destacado" class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                            <span class="ml-2 text-sm text-gray-700">Marcar como destacado</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" wire:model="comentarios_habilitados" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Habilitar comentarios</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="flex justify-end space-x-4 px-6 py-4 border-t border-gray-200 bg-gray-50">
                <a href="{{ route('admin.blog.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg">Cancelar</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">Publicar Articulo</button>
            </div>
        </form>
    </div>
</div>