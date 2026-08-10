<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Blog y Noticias</h1>
            <p class="text-xl text-gray-600">Enterate de las ultimas novedades de la Fundacion YAMA</p>
        </div>

        @if($destacados->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
                @foreach($destacados as $destacado)
                    <a href="{{ route('blog.detalle', $destacado) }}" class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow group">
                        @if($destacado->imagen_portada)
                            <img src="{{ asset('storage/'.$destacado->imagen_portada) }}" class="w-full h-48 object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-48 bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                <span class="text-white text-4xl font-bold">YAMA</span>
                            </div>
                        @endif
                        <div class="p-6">
                            <span class="text-xs font-semibold text-blue-600 uppercase">Destacado</span>
                            <h2 class="text-lg font-bold text-gray-900 mt-2 line-clamp-2">{{ $destacado->titulo }}</h2>
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $destacado->resumen }}</p>
                            <div class="flex items-center text-xs text-gray-500 mt-4">
                                <span>{{ $destacado->fecha_publicacion->format('d M Y') }}</span>
                                <span class="mx-2">·</span>
                                <span>{{ $destacado->tiempo_lectura ?? 5 }} min lectura</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Search and Filter -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar articulos..."
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500" />
                </div>
                <div>
                    <select wire:model.live="categoria" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">Todas las categorias</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat }}">{{ ucfirst($cat) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Articles Grid -->
        @if($articulos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($articulos as $articulo)
                    <a href="{{ route('blog.detalle', $articulo) }}" class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow group">
                        @if($articulo->imagen_portada)
                            <img src="{{ asset('storage/'.$articulo->imagen_portada) }}" class="w-full h-44 object-cover group-hover:scale-105 transition-transform">
                        @else
                            <div class="w-full h-44 bg-gradient-to-r from-gray-400 to-gray-600 flex items-center justify-center">
                                <span class="text-white text-2xl font-bold">YAMA</span>
                            </div>
                        @endif
                        <div class="p-5">
                            <span class="text-xs font-semibold text-purple-600 uppercase">{{ $articulo->categoria }}</span>
                            <h3 class="text-base font-bold text-gray-900 mt-2 line-clamp-2">{{ $articulo->titulo }}</h3>
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $articulo->resumen }}</p>
                            <div class="flex items-center text-xs text-gray-500 mt-4">
                                <span>{{ $articulo->fecha_publicacion->format('d M Y') }}</span>
                                <span class="mx-2">·</span>
                                <span>{{ $articulo->tiempo_lectura ?? 5 }} min lectura</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
            <div class="mt-8">{{ $articulos->links() }}</div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <h3 class="text-xl font-semibold text-gray-900">Sin articulos</h3>
                <p class="text-gray-600 mt-2">No hay articulos publicados en esta categoria.</p>
            </div>
        @endif
    </div>
</div>