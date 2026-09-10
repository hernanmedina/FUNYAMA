<div class="min-h-screen bg-gray-50">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('eventos.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-8">
            ← Volver a Eventos
        </a>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            @if($evento->imagen)
                <img src="{{ asset('storage/' . $evento->imagen) }}"
                     alt="{{ $evento->titulo }}"
                     class="w-full h-64 md:h-96 object-cover">
            @endif

            <div class="p-8 md:p-12">
                <!-- Encabezado -->
                <div class="flex items-center gap-3 mb-4 flex-wrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                        {{ $evento->tipo_evento === 'presencial' ? 'bg-blue-100 text-blue-800' :
                           ($evento->tipo_evento === 'virtual' ? 'bg-purple-100 text-purple-800' : 'bg-green-100 text-green-800') }}">
                        {{ ucfirst($evento->tipo_evento) }}
                    </span>
                    @if($evento->destacado)
                        <span class="inline-flex items-center gap-1 text-yellow-500">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                            <span class="text-xs font-medium">Destacado</span>
                        </span>
                    @endif
                </div>

                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">{{ $evento->titulo }}</h1>

                <!-- Información Básica -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 mb-1">Fecha</p>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $evento->fecha->format('d \d\e F \d\e Y') }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 mb-1">Hora</p>
                        <p class="text-base font-semibold text-gray-900">
                            {{ $evento->hora_inicio }} - {{ $evento->hora_fin }}
                        </p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 mb-1">Tipo</p>
                        <p class="text-base font-semibold text-gray-900">{{ ucfirst($evento->tipo_evento) }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-gray-600 mb-1">Costo</p>
                        <p class="text-base font-semibold text-gray-900">
                            @if($evento->costo > 0)
                                ${{ number_format($evento->costo, 2) }}
                            @else
                                Gratuito
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Descripción -->
                <div class="mb-8">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Descripción</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $evento->descripcion }}</p>
                </div>

                <!-- Contenido Detallado -->
                @if($evento->contenido)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Detalles</h2>
                        <div class="prose max-w-none text-gray-700 leading-relaxed">
                            {!! nl2br(e($evento->contenido)) !!}
                        </div>
                    </div>
                @endif

                <!-- Ubicación -->
                @if($evento->ubicacion || $evento->direccion || $evento->ciudad)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Ubicación</h2>
                        <div class="space-y-2">
                            @if($evento->ubicacion)
                                <p class="flex items-center gap-2 text-gray-700">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    {{ $evento->ubicacion }}
                                </p>
                            @endif
                            @if($evento->direccion)
                                <p class="text-gray-600 text-sm">{{ $evento->direccion }}</p>
                            @endif
                            @if($evento->ciudad)
                                <p class="text-gray-600 text-sm">{{ $evento->ciudad }}</p>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Enlace Virtual -->
                @if($evento->enlace_virtual)
                    <div class="mb-8">
                        <h2 class="text-xl font-semibold text-gray-900 mb-3">Acceso Virtual</h2>
                        <a href="{{ $evento->enlace_virtual }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Acceder a la transmisión
                        </a>
                    </div>
                @endif

                <!-- Cupo -->
                @if($evento->cupo_maximo)
                    <div class="mb-8">
                        <p class="text-sm font-medium text-gray-600 mb-2">Disponibilidad</p>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                 style="width: {{ min(100, ($evento->inscritos_actual / $evento->cupo_maximo) * 100) }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600 mt-2">
                            {{ $evento->inscritos_actual }}/{{ $evento->cupo_maximo }} participantes
                        </p>
                    </div>
                @endif

                <!-- Acciones -->
                <div class="pt-6 border-t border-gray-200 flex flex-wrap gap-3">
                    @if($evento->cupo_maximo === null || $evento->inscritos_actual < $evento->cupo_maximo)
                        <button wire:click="abrirInscripcion()"
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                            Inscribirme
                        </button>
                    @else
                        <span class="px-6 py-3 bg-red-100 text-red-700 rounded-lg font-medium">
                            Cupos agotados
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Eventos relacionados -->
        @if($relacionados->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Otros eventos próximos</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($relacionados as $relacionado)
                        <a href="{{ route('eventos.show', $relacionado) }}"
                           class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow overflow-hidden">
                            @if($relacionado->imagen)
                                <img src="{{ asset('storage/' . $relacionado->imagen) }}"
                                     alt="{{ $relacionado->titulo }}"
                                     class="w-full h-40 object-cover">
                            @endif
                            <div class="p-4">
                                <p class="text-xs text-gray-500 mb-1">{{ $relacionado->fecha->format('d M Y') }}</p>
                                <h3 class="font-semibold text-gray-900 line-clamp-2">{{ $relacionado->titulo }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de Inscripción al Evento -->
    @if($mostrarModalInscripcion)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <!-- Header del Modal -->
                <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Inscripción al Evento</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Evento: <span class="font-semibold">{{ $evento->titulo }}</span>
                        </p>
                    </div>
                    <button wire:click="cerrarModalInscripcion()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Modal -->
                <div class="p-6 space-y-6">
                    <!-- Información del Evento -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Información del Evento</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Fecha:</p>
                                <p class="font-medium text-gray-900">
                                    {{ $evento->fecha->format('d \d\e F \d\e Y') }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Hora:</p>
                                <p class="font-medium text-gray-900">
                                    {{ $evento->hora_inicio }} - {{ $evento->hora_fin }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Costo:</p>
                                <p class="font-medium text-gray-900">
                                    @if($evento->costo > 0)
                                        ${{ number_format($evento->costo, 2) }}
                                    @else
                                        Gratuito
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-600">Cupos disponibles:</p>
                                <p class="font-medium text-gray-900">
                                    @if($evento->cupo_maximo)
                                        {{ $evento->cupo_maximo - $evento->inscritos_actual }}
                                    @else
                                        Ilimitados
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Datos del inscrito -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Tus Datos</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="nombre" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu nombre">
                                @error('nombre') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                                <input wire:model="apellido" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('apellido') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu apellido">
                                @error('apellido') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="email" type="email"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="tucorreo@ejemplo.com">
                                @error('email') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input wire:model="telefono" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefono') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu teléfono">
                                @error('telefono') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Documento de identidad</label>
                                <input wire:model="documento" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('documento') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu documento de identidad">
                                @error('documento') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    @guest
                        <p class="text-xs text-gray-500 bg-gray-50 rounded-lg p-3">
                            Si ya tienes una cuenta, <a href="{{ route('login') }}" class="text-blue-600 hover:underline">inicia sesión</a>
                            para que tu inscripción quede vinculada a tu perfil.
                        </p>
                    @endguest
                </div>

                <!-- Footer del Modal -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 flex justify-end gap-3">
                    <button wire:click="cerrarModalInscripcion()"
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button wire:click="confirmarInscripcion()"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <span wire:loading.remove wire:target="confirmarInscripcion">Confirmar inscripción</span>
                        <span wire:loading wire:target="confirmarInscripcion">Procesando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Script para toast notifications -->
<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-toast', (event) => {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white ${
                event.type === 'success' ? 'bg-green-600' :
                event.type === 'warning' ? 'bg-yellow-500' : 'bg-red-600'
            }`;
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="flex items-center gap-3">
                    <span>${event.message}</span>
                    <button onclick="this.parentElement.parentElement.remove()" class="ml-2 text-white hover:text-gray-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        });
    });
</script>
