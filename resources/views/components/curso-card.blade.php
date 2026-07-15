@props(['curso', 'estaInscrito' => false])

<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 hover:shadow-xl transition-shadow duration-300 flex flex-col h-full">

    <!-- Imagen de portada -->
    <div class="relative">
        @if($curso->imagen_portada)
            <img src="{{ asset('storage/' . $curso->imagen_portada) }}"
                 alt="{{ $curso->nombre }}"
                 class="w-full h-44 object-cover">
        @else
            <div class="w-full h-44 bg-gradient-to-br from-blue-700 to-purple-800 flex items-center justify-center">
                <svg class="w-14 h-14 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
        @endif

        <!-- Badge de nivel -->
        <span class="absolute top-3 left-3 bg-white/90 text-gray-800 text-xs font-semibold px-2.5 py-1 rounded-full shadow">
            {{ ucfirst($curso->nivel ?? 'Todos') }}
        </span>

        <!-- Badge de descuento -->
        @if($curso->precio_descuento)
            @php
                $descuento = (($curso->precio_regular - $curso->precio_descuento) / $curso->precio_regular) * 100;
            @endphp
            <span class="absolute top-3 right-3 bg-red-500 text-white text-xs font-bold px-2.5 py-1 rounded-full shadow">
                -{{ round($descuento) }}%
            </span>
        @endif

        <!-- Cupos bajos -->
        @if($curso->cupo_disponible <= 5 && $curso->cupo_disponible > 0)
            <span class="absolute bottom-3 left-3 bg-orange-500 text-white text-xs font-semibold px-2.5 py-1 rounded-full shadow">
                ⚡ Últimos cupos
            </span>
        @endif
    </div>

    <!-- Contenido -->
    <div class="p-5 flex flex-col flex-1 space-y-3">

        <!-- Título -->
        <a href="{{ route('cursos.show', $curso->codigo) }}" class="group">
            <h3 class="text-lg font-bold text-gray-900 leading-tight line-clamp-2 group-hover:text-blue-700 transition-colors">
                {{ $curso->nombre }}
            </h3>
        </a>

        <!-- Descripción -->
        <p class="text-sm text-gray-500 line-clamp-2">
            {{ Str::limit($curso->descripcion, 120) }}
        </p>

        <!-- Info meta -->
        <div class="flex items-center gap-4 text-xs text-gray-400">
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                {{ $curso->duracion_texto ?? 'Flexible' }}
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                {{ $curso->estudiantes->count() }} est.
            </span>
            <span class="flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                Certificado
            </span>
        </div>

        <!-- Barra de progreso de cupos -->
        <div class="space-y-1">
            <div class="w-full bg-gray-100 rounded-full h-1.5">
                @php
                    $porcentajeCupos = $curso->cupo_total > 0
                        ? (($curso->cupo_total - $curso->cupo_disponible) / $curso->cupo_total) * 100
                        : 0;
                @endphp
                <div class="h-1.5 rounded-full {{ $porcentajeCupos > 80 ? 'bg-red-500' : ($porcentajeCupos > 50 ? 'bg-orange-400' : 'bg-green-500') }}"
                     style="width: {{ $porcentajeCupos }}%"></div>
            </div>
            <p class="text-xs text-gray-400">
                {{ $curso->cupo_disponible }} de {{ $curso->cupo_total }} cupos disponibles
            </p>
        </div>

        <!-- Separador -->
        <div class="border-t border-gray-100"></div>

        <!-- Precio y botón -->
        <div class="flex items-end justify-between mt-auto pt-1">
            <div>
                @if($curso->precio_descuento)
                    <span class="text-xs text-gray-400 line-through">${{ number_format($curso->precio_regular, 0) }}</span>
                @endif
                <p class="text-xl font-bold text-gray-900">
                    ${{ number_format($curso->precioFinal, 0) }}
                    <span class="text-xs font-normal text-gray-400">COP</span>
                </p>
            </div>
            @if($estaInscrito)
                <span class="bg-green-100 text-green-700 text-sm font-semibold px-4 py-2 rounded-lg border border-green-200">
                    ✓ Ya inscrito
                </span>
            @else
                <button wire:click="abrirModalSolicitud('{{ $curso->codigo }}')"
                        class="bg-blue-700 hover:bg-blue-800 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-colors duration-200 shadow-sm hover:shadow">
                    Inscribirse
                </button>
            @endif
        </div>
    </div>
</div>

