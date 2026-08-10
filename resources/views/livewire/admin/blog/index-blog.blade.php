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
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
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
                                <td class="px-6 py-4"><span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-700">{{ ucfirst($articulo->categoria) }}</span></td>
                                <td class="px-6 py-4 text-center text-sm">{{ $articulo->vistas }}</td>
                                <td class="px-6 py-4 text-center">{{ $articulo->publicado ? "\u2705" : "\u274C" }}</td>
                                <td class="px-6 py-4 text-center">{{ $articulo->destacado ? "\u2B50" : "\u2606" }}</td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('admin.blog.edit', $articulo) }}" class="text-blue-600 hover:text-blue-800 mr-3">Editar</a>
                                    <a href="{{ route('blog.detalle', $articulo) }}" target="_blank" class="text-gray-500 hover:text-gray-700 mr-3">Ver</a>
                                    <button wire:click="eliminar({{ $articulo->idPost }})" wire:confirm="Eliminar este articulo?" class="text-red-500 hover:text-red-700">Eliminar</button>
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
</div>