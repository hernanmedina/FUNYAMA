<!-- Eventos Destacados Section -->
<section id="eventos" class="bg-gray-50 py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">Próximos Eventos</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                Únete a nuestros próximos eventos educativos, conferencias y talleres diseñados para fortalecer tus conocimientos y habilidades.
            </p>
        </div>

        @if($eventos->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach($eventos as $evento)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1">
                        <!-- Event Image -->
                        <div class="relative h-48 bg-gradient-to-br from-blue-500 to-purple-600 overflow-hidden">
                            @if($evento->imagen)
                                <img src="{{ asset('storage/' . $evento->imagen) }}" 
                                     alt="{{ $evento->titulo }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <svg class="w-16 h-16 text-white opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <!-- Event Type Badge -->
                            <div class="absolute top-4 right-4">
                                <span class="bg-blue-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                                    {{ ucfirst($evento->tipo_evento) }}
                                </span>
                            </div>
                        </div>

                        <!-- Event Content -->
                        <div class="p-6">
                            <!-- Title -->
                            <h3 class="text-xl font-bold text-gray-800 mb-2 line-clamp-2">
                                {{ $evento->titulo }}
                            </h3>

                            <!-- Description -->
                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                {{ Str::limit($evento->descripcion, 100) }}
                            </p>

                            <!-- Event Details -->
                            <div class="space-y-3 mb-6">
                                <!-- Date -->
                                <div class="flex items-center text-gray-700">
                                    <svg class="w-5 h-5 text-blue-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="font-medium">
                                        {{ $evento->fecha->format('d \d\e F \d\e Y') }}
                                    </span>
                                </div>

                                <!-- Time -->
                                @if($evento->hora_inicio)
                                    <div class="flex items-center text-gray-700">
                                        <svg class="w-5 h-5 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span class="font-medium">
                                            @if(is_object($evento->hora_inicio))
                                                {{ $evento->hora_inicio->format('H:i') }} - {{ $evento->hora_fin->format('H:i') }}
                                            @else
                                                {{ substr($evento->hora_inicio, 0, 5) }} - {{ substr($evento->hora_fin, 0, 5) }}
                                            @endif
                                        </span>
                                    </div>
                                @endif

                                <!-- Location -->
                                @if($evento->ubicacion)
                                    <div class="flex items-center text-gray-700">
                                        <svg class="w-5 h-5 text-red-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <span class="font-medium text-sm">{{ $evento->ubicacion }}</span>
                                    </div>
                                @endif

                                <!-- Capacity -->
                                @if($evento->cupo_maximo)
                                    <div class="flex items-center text-gray-700">
                                        <svg class="w-5 h-5 text-purple-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-12 0z"/>
                                        </svg>
                                        <span class="font-medium text-sm">
                                            {{ $evento->inscritos_actual }}/{{ $evento->cupo_maximo }} Inscritos
                                        </span>
                                    </div>
                                @endif
                            </div>

                            <!-- Cost -->
                            @if($evento->costo > 0)
                                <div class="mb-4 p-3 bg-amber-50 rounded-lg">
                                    <span class="text-lg font-bold text-amber-700">
                                        ${{ number_format($evento->costo, 2, ',', '.') }}
                                    </span>
                                </div>
                            @else
                                <div class="mb-4 p-3 bg-green-50 rounded-lg">
                                    <span class="text-lg font-bold text-green-700">Evento Gratuito</span>
                                </div>
                            @endif

                            <!-- CTA Button -->
                            <a href="{{ route('eventos.index') }}" 
                               class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 rounded-lg transition duration-200 flex items-center justify-center">
                                Ver Detalles
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- View All Events Button -->
            <div class="mt-12 text-center">
                <a href="{{ route('eventos.index') }}" 
                   class="inline-block bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-8 rounded-lg transition duration-200 shadow-lg">
                    Ver Todos los Eventos
                    <svg class="w-5 h-5 inline-block ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </a>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <h3 class="text-xl font-bold text-gray-800 mb-2">No hay eventos disponibles</h3>
                <p class="text-gray-600 mb-6">
                    Por el momento no hay eventos publicados. Vuelve pronto para ver nuevos eventos.
                </p>
                <a href="{{ route('eventos.index') }}" 
                   class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200">
                    Ver Calendario de Eventos
                </a>
            </div>
        @endif
    </div>
</section>
