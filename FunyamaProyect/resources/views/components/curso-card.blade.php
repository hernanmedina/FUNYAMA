<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200 grid md:grid-cols-3 gap-0">

    <!-- SECCIÓN IZQUIERDA -->
    <div class="md:col-span-2 bg-gray-900 text-white p-6 space-y-4">

        <!-- Breadcrumb -->
        {{--        <p class="text-sm text-gray-400">--}}
        {{--            Productividad en la oficina › Microsoft › Excel--}}
        {{--        </p>--}}

        <!-- Título -->
        <h2 class="text-2xl md:text-3xl font-bold">
            {{ $curso->nombre }}
        </h2>

        <!-- Descripción -->
        <p class="text-gray-300">
            {{ Str::limit($curso->descripcion, 150) }}
        </p>

        <!-- Info del curso -->
        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-300">
            <span>⭐ 4.7</span>
            <span>👥 {{ $curso->estudiantes->count() }} estudiantes</span>
            <span>🕒 {{ $curso->duracion_texto ?? 'Flexible' }}</span>
        </div>

        <div>
            @if($curso->imagen_portada)
                <img src="{{ asset('storage/' . $curso->imagen_portada) }}"
                     alt="{{ $curso->nombre }}"
                     class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gray-700 flex items-center justify-center">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
            @endif
        </div>
    </div>

    <!-- SECCIÓN DERECHA (PRECIO / ACCIÓN) -->
    <div class="p-6 bg-white space-y-4 border-t md:border-t-0 md:border-l">

        <!-- Precio -->
        <div>
            <p class="text-3xl font-bold text-gray-900">
                ${{ number_format($curso->precioFinal, 2) }} COP
            </p>
            @if($curso->precio_descuento)
                <p class="text-sm text-gray-500 line-through">
                    ${{ number_format($curso->precio_regular, 2) }} COP
                </p>
                @php
                    $descuento = (($curso->precio_regular - $curso->precio_descuento) / $curso->precio_regular) * 100;
                @endphp
                <p class="text-sm text-red-600 font-semibold">
                    {{ round($descuento) }}% de descuento
                </p>
            @endif
        </div>

        <!-- Botones -->
        <div class="space-y-3">
            @auth
                @if(auth()->user()->role === 'estudiante' || auth()->user()->role === 'estu')
                    <button wire:click="$dispatch('inscribir-curso', { cursoId: {{ $curso->idCurso }} })"
                            class="w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2 rounded-lg transition">
                        Inscribirse en este Curso
                    </button>
                @else
                    <a href="{{ route('login') }}"
                       class="block w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2 rounded-lg transition text-center">
                        Iniciar Sesión para Inscribirse
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}"
                   class="block w-full bg-blue-700 hover:bg-blue-900 text-white font-semibold py-2 rounded-lg transition text-center">
                    Iniciar Sesión para Inscribirse
                </a>
            @endauth
        </div>

        <!-- Detalles -->
        <ul class="text-sm text-gray-600 space-y-1">
            <li>✔ Certificado incluido</li>
            <li>✔ Acceso de por vida</li>
            <li>✔ {{ $curso->duracion_horas ?? 'Flexible' }} horas de contenido</li>
            <li>✔ Nivel: {{ ucfirst($curso->nivel) }}</li>
        </ul>

        <hr>

        <!-- Cupo disponible -->
        <div class="text-sm">
            <h3 class="font-semibold text-gray-800 mb-1">Cupos disponibles</h3>
            <p class="text-gray-600">
                {{ $curso->cupo_disponible }} de {{ $curso->cupo_total }} cupos
            </p>
            @if($curso->cupo_disponible <= 5 && $curso->cupo_disponible > 0)
                <p class="text-red-600 font-medium mt-1">
                    ⚠️ Quedan pocos cupos
                </p>
            @endif
        </div>

        <!-- Requisitos -->
        <div>
            <h3 class="font-semibold text-gray-800 mb-1">Requisitos</h3>
            <p class="text-sm text-gray-600">
                {{ Str::limit($curso->requisitos, 100) }}
            </p>
        </div>
    </div>
</div>
