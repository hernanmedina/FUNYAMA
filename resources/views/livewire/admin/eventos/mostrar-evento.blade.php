<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-start mb-6 gap-4 flex-wrap">
            <div>
                <a href="{{ route('admin.eventos.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-2">
                    ← Volver a Gestión de Eventos
                </a>
                <h1 class="text-3xl font-bold text-gray-800">{{ $evento->titulo }}</h1>
                <p class="text-gray-600 mt-1">Detalles completos del evento</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <button wire:click="togglePublicado"
                        class="bg-{{ $evento->publicado ? 'green' : 'gray' }}-600 hover:bg-{{ $evento->publicado ? 'green' : 'gray' }}-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ $evento->publicado ? 'Ocultar' : 'Publicar' }}
                </button>

                <button wire:click="toggleDestacado"
                        class="bg-{{ $evento->destacado ? 'yellow' : 'gray' }}-500 hover:bg-{{ $evento->destacado ? 'yellow' : 'gray' }}-600 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                    </svg>
                    {{ $evento->destacado ? 'Sin destacar' : 'Destacar' }}
                </button>

                <a href="{{ route('admin.eventos.edit', $evento->idEvento) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>

                <a href="{{ route('admin.eventos.inscripciones', $evento->idEvento) }}"
                   class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Inscripciones
                </a>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="mb-6 p-4 bg-green-100 text-green-700 rounded-lg">
                {{ session('message') }}
            </div>
        @endif

        <!-- Métricas del evento -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-green-100 text-green-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Inscritos confirmados</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalInscritos }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-amber-100 text-amber-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Ingresos recaudados</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($ingresos, 2) }}</p>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 flex items-center">
                <div class="p-3 rounded-full bg-red-100 text-red-600 mr-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pagos pendientes</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $pagosPendientes }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información principal -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    @if($evento->imagen)
                        <img src="{{ asset('storage/' . $evento->imagen) }}"
                             alt="{{ $evento->titulo }}"
                             class="w-full h-64 object-cover">
                    @endif
                    <div class="p-6">
                        <div class="flex items-center gap-3 mb-4 flex-wrap">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $evento->tipo_evento === 'presencial' ? 'bg-blue-100 text-blue-800' :
                                   ($evento->tipo_evento === 'virtual' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                                {{ ucfirst($evento->tipo_evento) }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                                {{ $evento->publicado ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                {{ $evento->publicado ? 'Publicado' : 'No publicado' }}
                            </span>
                            @if($evento->destacado)
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Destacado
                                </span>
                            @endif
                        </div>

                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Descripción</h2>
                        <p class="text-gray-700 leading-relaxed mb-6">{{ $evento->descripcion }}</p>

                        @if($evento->contenido)
                            <h2 class="text-xl font-semibold text-gray-900 mb-3">Detalles</h2>
                            <div class="prose max-w-none text-gray-700 leading-relaxed">
                                {!! nl2br(e($evento->contenido)) !!}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Últimas inscripciones -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Últimas inscripciones</h2>
                        <a href="{{ route('admin.eventos.inscripciones', $evento->idEvento) }}"
                           class="text-sm text-purple-600 hover:text-purple-800 font-medium">
                            Ver todas
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase">Pago</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($inscripciones as $inscripcion)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            {{ $inscripcion->nombre }} {{ $inscripcion->apellido }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">{{ $inscripcion->email }}</td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $inscripcion->estado === 'confirmada' ? 'bg-green-100 text-green-800' :
                                                   ($inscripcion->estado === 'cancelada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ ucfirst($inscripcion->estado) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                {{ $inscripcion->pago_realizado ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
                                                {{ $inscripcion->pago_realizado ? 'Pagado' : 'Pendiente' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                            Aún no hay inscripciones para este evento.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Panel lateral -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Información del evento</h2>
                    <dl class="space-y-4 text-sm">
                        <div>
                            <dt class="font-medium text-gray-600">Fecha</dt>
                            <dd class="text-gray-900">{{ $evento->fecha->format('d \d\e F \d\e Y') }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-600">Hora</dt>
                            <dd class="text-gray-900">{{ $evento->hora_inicio }} - {{ $evento->hora_fin }}</dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-600">Costo</dt>
                            <dd class="text-gray-900">
                                @if($evento->costo > 0)
                                    ${{ number_format($evento->costo, 2) }}
                                @else
                                    Gratuito
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="font-medium text-gray-600">Cupo</dt>
                            <dd class="text-gray-900">
                                @if($evento->cupo_maximo)
                                    {{ $evento->inscritos_actual }}/{{ $evento->cupo_maximo }} participantes
                                @else
                                    Ilimitado
                                @endif
                            </dd>
                        </div>
                        @if($evento->ubicacion)
                            <div>
                                <dt class="font-medium text-gray-600">Ubicación</dt>
                                <dd class="text-gray-900">{{ $evento->ubicacion }}</dd>
                            </div>
                        @endif
                        @if($evento->direccion)
                            <div>
                                <dt class="font-medium text-gray-600">Dirección</dt>
                                <dd class="text-gray-900">{{ $evento->direccion }}</dd>
                            </div>
                        @endif
                        @if($evento->ciudad)
                            <div>
                                <dt class="font-medium text-gray-600">Ciudad</dt>
                                <dd class="text-gray-900">{{ $evento->ciudad }}</dd>
                            </div>
                        @endif
                        @if($evento->enlace_virtual)
                            <div>
                                <dt class="font-medium text-gray-600">Enlace virtual</dt>
                                <dd>
                                    <a href="{{ $evento->enlace_virtual }}" target="_blank" rel="noopener noreferrer"
                                       class="text-blue-600 hover:text-blue-800 break-all">
                                        {{ $evento->enlace_virtual }}
                                    </a>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>

                @if($evento->cupo_maximo)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Disponibilidad</h2>
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                 style="width: {{ min(100, ($evento->inscritos_actual / $evento->cupo_maximo) * 100) }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600">
                            {{ $evento->cupo_maximo - $evento->inscritos_actual }} cupos disponibles
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
