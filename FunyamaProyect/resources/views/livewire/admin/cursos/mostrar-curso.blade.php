<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $curso->nombre }}</h1>
                <p class="text-gray-600 mt-1">Detalles completos del curso</p>
            </div>
            <div class="flex space-x-3">
                <button wire:click="togglePublicacion"
                        class="bg-{{ $curso->publicado ? 'green' : 'gray' }}-600 hover:bg-{{ $curso->publicado ? 'green' : 'gray' }}-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ $curso->publicado ? 'Ocultar' : 'Publicar' }}
                </button>
                <a href="{{ route('admin.cursos.edit', $curso->idCurso) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <button wire:click="eliminarCurso"
                        wire:confirm="¿Estás seguro de eliminar este curso?"
                        class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar
                </button>
                <a href="{{ route('admin.cursos.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-blue-600 text-sm font-medium">Estudiantes Inscritos</div>
                <div class="text-2xl font-bold text-blue-700">{{ $estudiantes->count() }}</div>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <div class="text-green-600 text-sm font-medium">Cupos Disponibles</div>
                <div class="text-2xl font-bold text-green-700">{{ $curso->cupo_disponible }}</div>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <div class="text-yellow-600 text-sm font-medium">Cupo Total</div>
                <div class="text-2xl font-bold text-yellow-700">{{ $curso->cupo_total }}</div>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <div class="text-purple-600 text-sm font-medium">Estado</div>
                <div class="text-2xl font-bold text-purple-700">
                    {{ $curso->publicado ? 'Publicado' : 'Oculto' }}
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información Principal -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Imagen y Descripción -->
                <div class="bg-white rounded-lg shadow p-6">
                    @if($curso->imagen_portada)
                        <img src="{{ asset('storage/' . $curso->imagen_portada) }}"
                             alt="{{ $curso->nombre }}"
                             class="w-full h-64 object-cover rounded-lg mb-4">
                    @endif
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Descripción</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $curso->descripcion }}</p>
                </div>

                <!-- Detalles del Curso -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Detalles del Curso</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Cronograma</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ $curso->cronograma }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Requisitos</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ $curso->requisitos }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Objetivos</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ $curso->objetivos ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Materiales Incluidos</h3>
                            <p class="text-gray-600 whitespace-pre-line">{{ $curso->materiales_incluidos ?? 'No especificado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Información Lateral -->
            <div class="space-y-6">
                <!-- Información General -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Información General</h2>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">Nivel:</span>
                            <span class="ml-2 text-gray-700 capitalize">{{ $curso->nivel }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Duración:</span>
                            <span class="ml-2 text-gray-700">
                                {{ $curso->duracion_horas }} horas
                                @if($curso->duracion_texto)
                                    ({{ $curso->duracion_texto }})
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Fecha de Inicio:</span>
                            <span class="ml-2 text-gray-700">
                                {{ $curso->fecha_inicio ? $curso->fecha_inicio->format('d/m/Y') : 'No definida' }}
                            </span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Precio Regular:</span>
                            <span class="ml-2 text-gray-700">${{ number_format($curso->precio_regular, 2) }}</span>
                        </div>
                        @if($curso->precio_descuento)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Precio con Descuento:</span>
                                <span class="ml-2 text-green-600 font-semibold">${{ number_format($curso->precio_descuento, 2) }}</span>
                            </div>
                        @endif
                        <div>
                            <span class="text-sm font-medium text-gray-500">Estado:</span>
                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                      {{ $curso->publicado ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $curso->publicado ? 'Publicado' : 'Oculto' }}
                            </span>
                        </div>
                        @if($curso->destacado)
                            <div>
                                <span class="text-sm font-medium text-gray-500">Destacado:</span>
                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Sí
                                </span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Estudiantes Inscritos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Estudiantes Inscritos</h2>
                    @if($estudiantes->count() > 0)
                        <div class="space-y-3">
                            @foreach($estudiantes as $estudiante)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                            {{ substr($estudiante->user->name, 0, 1) }}{{ substr($estudiante->user->apellido, 0, 1) }}
                                        </div>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-gray-900 truncate">
                                            {{ $estudiante->user->name }} {{ $estudiante->user->apellido }}
                                        </p>
                                        <p class="text-sm text-gray-500 truncate">{{ $estudiante->user->email }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-sm">No hay estudiantes inscritos en este curso.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
