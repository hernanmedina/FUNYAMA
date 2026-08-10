<div class="min-h-screen bg-gray-50">
    <article class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('blog.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-8">
            ← Volver al Blog
        </a>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($articulo->imagen_portada)
                <img src="{{ asset('storage/'.$articulo->imagen_portada) }}" class="w-full h-64 md:h-96 object-cover">
            @endif
            <div class="p-8 md:p-12">
                <div class="flex items-center gap-3 mb-4 flex-wrap">
                    <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">{{ ucfirst($articulo->categoria) }}</span>
                    <span class="text-sm text-gray-500">{{ $articulo->fecha_publicacion->format('d \d\e F, Y') }}</span>
                    <span class="text-sm text-gray-500">· {{ $articulo->tiempo_lectura ?? 5 }} min lectura</span>
                    <span class="text-sm text-gray-500">· {{ $articulo->vistas }} vistas</span>
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ $articulo->titulo }}</h1>

                <div class="flex items-center mb-8">
                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-semibold">{{ strtoupper(substr($articulo->autor, 0, 1)) }}</span>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">{{ $articulo->autor }}</p>
                        @if($articulo->fuente)
                            <p class="text-xs text-gray-500">Fuente: {{ $articulo->fuente }}</p>
                        @endif
                    </div>
                </div>

                <div class="prose max-w-none text-gray-700 leading-relaxed text-lg">
                    {!! nl2br(e($articulo->contenido)) !!}
                </div>

                @if(count($articulo->etiquetas ?? []) > 0)
                    <div class="flex flex-wrap gap-2 mt-8 pt-8 border-t border-gray-200">
                        @foreach($articulo->etiquetas as $etiqueta)
                            <span class="px-3 py-1 text-xs bg-gray-100 text-gray-600 rounded-full">{{ $etiqueta }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @if($relacionados->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Articulos relacionados</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relacionados as $rel)
                        <a href="{{ route('blog.detalle', $rel) }}" class="bg-white rounded-lg shadow p-5 hover:shadow-md transition-shadow">
                            <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $rel->titulo }}</h3>
                            <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $rel->resumen }}</p>
                            <span class="text-xs text-gray-500 mt-3 inline-block">{{ $rel->fecha_publicacion->format('d M Y') }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </article>
</div>