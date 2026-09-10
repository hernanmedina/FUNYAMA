<div>
    <div class="w-full max-w-[1600px] mx-auto py-6 px-4">
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Blog y Noticias</h1>
                <p class="text-gray-600 mt-2">{{ $totalPublicados }} publicados de {{ $totalArticulos }} articulos</p>
            </div>
            <a href="{{ route('admin.blog.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                + Nuevo Articulo
            </a>
        </div>

        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Titulo o resumen..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Categoria</label>
                    <select wire:model.live="categoriaFilter" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas</option>
                        @foreach($categorias as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        @if($articulos->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Articulo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Categoria</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Vistas</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Publicado</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Destacado</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($articulos as $articulo)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        @if($articulo->imagen_portada)
                                            <img src="{{ asset('storage/'.$articulo->imagen_portada) }}" class="h-10 w-16 rounded object-cover mr-3">
                                        @endif
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $articulo->titulo }}</div>
                                            <div class="text-xs text-gray-500">{{ $articulo->created_at->format('d/m/Y') }} por {{ $articulo->autor }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ $categorias[$articulo->categoria] ?? ucfirst($articulo->categoria) }}</span></td>
                                <td class="px-6 py-4 text-center text-sm">{{ $articulo->vistas }}</td>
                                <td class="px-6 py-4 text-center">{{ $articulo->publicado ? '✅' : '❌' }}</td>
                                <td class="px-6 py-4 text-center">{{ $articulo->destacado ? '⭐' : '☆' }}</td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end space-x-2">
                                        <a href="{{ route('blog.detalle', $articulo) }}" target="_blank" class="text-blue-600 hover:text-blue-900" title="Ver">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.blog.edit', $articulo) }}" class="text-green-600 hover:text-green-900" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <button wire:click="eliminar({{ $articulo->idPost }})" wire:confirm="Eliminar este articulo?" class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="px-6 py-4 border-t">{{ $articulos->links() }}</div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <h3 class="text-lg font-semibold text-gray-900">Sin articulos</h3>
                <p class="text-gray-600 mt-2">No hay articulos que coincidan con los filtros.</p>
            </div>
        @endif
    </div>

    <!-- Script para toast notifications -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('show-toast', (event) => {
                const data = Array.isArray(event) ? event[0] : event;
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white ${
                    data.type === 'success' ? 'bg-green-500' :
                        data.type === 'error' ? 'bg-red-500' :
                            'bg-yellow-500'
                }`;
                toast.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="${data.type === 'success' ? 'M5 13l4 4L19 7' :
                data.type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                    'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z'}"/>
                        </svg>
                        ${data.message}
                    </div>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.remove();
                }, 3000);
            });
        });
    </script>
</div>
</div>