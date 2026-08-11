<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $curso->nombre }}</h1>
                <p class="text-gray-600 mt-1">Detalles completos del curso</p>
            </div>
            <div class="flex flex-wrap gap-3">
                @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="flex flex-wrap gap-3">
                        <button wire:click="togglePublicacion"
                                class="bg-{{ $curso->publicado ? 'green' : 'gray' }}-600 hover:bg-{{ $curso->publicado ? 'green' : 'gray' }}-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            {{ $curso->publicado ? 'Ocultar' : 'Publicar' }}
                        </button>

                        <a href="{{ route('admin.cursos.edit', $curso->codigo) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Editar
                        </a>

                        <button wire:click="eliminarCurso"
                                wire:confirm="¿Estás seguro de eliminar este curso?"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Eliminar
                        </button>
                    </div>
                @endif

                <a href="{{ (auth()->check() && auth()->user()->isAdmin()) ? route('admin.cursos.index') : route('cursos.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center whitespace-nowrap">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>

                @if(!auth()->check() || !auth()->user()->isAdmin())
                    @if(auth()->check() && auth()->user()->isEstudiante() && $estaInscrito)
                        <span class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded-lg font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Ya estás inscrito
                        </span>
                    @else
                        <button wire:click="abrirModalSolicitud"
                                class="bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Inscribirse
                        </button>
                    @endif
                @endif
            </div>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-blue-600 text-sm font-medium">Estudiantes Inscritos</div>
                @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="text-2xl font-bold text-blue-700">{{ $estudiantes->count() }}</div>
                @else
                    <div class="text-2xl font-bold text-blue-700">Privado</div>
                @endif
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
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Horarios</h3>
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
                        <div class="md:col-span-2">
                            <h2 class="text-xl font-semibold text-gray-800 mb-4">Información Importante</h2>
                            <p class="text-gray-600 whitespace-pre-line">- La información de los estudiantes inscritos es privada.</p>
                            <p class="text-gray-600 whitespace-pre-line">- Asegurece de haber marcado todas las casillas que realizó.</p>
                            <p class="text-gray-600 whitespace-pre-line">- Recuerde que una vez finalizado el curso, este no podrá ser modificado.</p>
                            <p class="text-gray-600 whitespace-pre-line">- Para marcar un curso como finalizado, debe hacerlo desde la sección de mis cursos inscritos.</p>
                        </div>
                        <div class="md:col-span-2">
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Temario</h3>
                            @if($curso->temario)
                                <div class="w-full rounded-lg border border-gray-300 bg-gray-50 p-4 text-gray-700 whitespace-pre-line leading-relaxed min-h-[180px]">
                                    {{ $curso->temario }}
                                </div>
                            @else
                                <p class="text-gray-600">No hay temario definido.</p>
                            @endif
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
                        @if($curso->enlace_classroom && auth()->check() && (auth()->user()->isAdmin() || (auth()->user()->isEstudiante() && $estaInscrito)))
                            <div>
                                <span class="text-sm font-medium text-gray-500">Classroom:</span>
                                {{-- <a href="{{ $curso->enlace_classroom }}" target="_blank" rel="noopener noreferrer" class="ml-2 text-blue-600 hover:underline">Abrir enlace</a> --}}
                            </div>
                            <div class="pt-2">
                                <a href="{{ $curso->enlace_classroom }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
                                    Ir a Classroom
                                </a>
                            </div>
                        @endif
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

                @if(auth()->check() && auth()->user()->isEstudiante() && $estaInscrito)
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800">Progreso del Curso</h2>
                            <div class="text-sm font-medium text-blue-700">{{ number_format($progresoPorcentaje ?? 0, 2) }}%</div>
                        </div>
                        @if($curso->temario_items)
                            <div class="space-y-3">
                                @foreach($curso->temario_items as $index => $item)
                                    <label class="flex items-center gap-3 p-2 rounded-lg border border-gray-200 {{ ($estadoCurso === 'completado') ? 'opacity-50 cursor-not-allowed' : 'hover:bg-gray-50 cursor-pointer' }}">
                                        <input type="checkbox"
                                               wire:click="toggleTema({{ $index }})"
                                               @checked(!empty($temarioProgreso[$index]))
                                               @disabled($estadoCurso === 'completado')
                                               class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        <span class="text-sm text-gray-700">{{ $item }}</span>
                                    </label>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 text-sm">Aún no hay temario definido para este curso.</p>
                        @endif
                    </div>
                @elseif(auth()->check() && auth()->user()->isEstudiante() && !$estaInscrito)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Progreso del Curso</h2>
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            <p class="text-gray-600 font-medium">No estás inscrito en este curso</p>
                            <p class="text-gray-500 text-sm mt-1">Debes inscribirte primero para poder ver y registrar tu progreso.</p>
                            <button wire:click="abrirModalSolicitud"
                                    class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                                </svg>
                                Inscribirme Ahora
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Estudiantes Inscritos -->
                @if(auth()->check() && auth()->user()->isAdmin())
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
                @else
                    <div class="bg-white rounded-lg shadow p-6">
                        <h2 class="text-xl font-semibold text-gray-800 mb-4">Contacto</h2>
                        <p class="text-gray-500 text-sm">¿Tienes preguntas sobre el curso? Contáctanos para más información.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Footer de contacto -->
    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="container mx-auto py-8 px-4 text-center">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Síguenos en nuestras redes</h3>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="https://youtube.com/@fundacionyamacapacitacione6929" target="_blank" rel="noopener noreferrer" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                    YouTube
                </a>
                <a href="https://www.tiktok.com/@profebarragan" target="_blank" rel="noopener noreferrer" class="bg-black hover:bg-gray-800 text-white px-4 py-2 rounded-lg flex items-center">
                    TikTok
                </a>
                <a href="https://www.facebook.com/profile.php?id=100086488552580" target="_blank" rel="noopener noreferrer" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    Facebook
                </a>
            </div>
        </div>
    </footer>

    <!-- Modal de Solicitud de Inscripción -->
    @if($mostrarModalSolicitud)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header del Modal -->
                <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Solicitar Inscripción</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Curso: <span class="font-semibold">{{ $curso->nombre }}</span>
                        </p>
                    </div>
                    <button wire:click="cerrarModal"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Contenido del Modal -->
                <div class="p-6 space-y-6">
                    <!-- Información del Curso -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Información del Curso</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Nombre:</p>
                                <p class="font-medium text-gray-900">{{ $curso->nombre }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Precio:</p>
                                <p class="font-medium text-gray-900">${{ number_format($curso->precioFinal, 2) }} COP</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Duración:</p>
                                <p class="font-medium text-gray-900">{{ $curso->duracion_texto ?? ($curso->duracion_horas ? $curso->duracion_horas.' horas' : 'Flexible') }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Nivel:</p>
                                <p class="font-medium text-gray-900 capitalize">{{ $curso->nivel }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- Formulario de Datos Personales -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Tus Datos</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="nombre_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu nombre">
                                @error('nombre_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                                <input wire:model="apellido_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('apellido_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu apellido">
                                @error('apellido_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="email_solicitante" type="email"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="correo@ejemplo.com">
                                @error('email_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Documento de Identidad <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="documento_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('documento_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Cédula, NIT, etc.">
                                @error('documento_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                                <input wire:model="telefono_solicitante" type="tel"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefono_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu número de contacto">
                                @error('telefono_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Dirección</label>
                                <input wire:model="direccion_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu dirección">
                                @error('direccion_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Nacimiento</label>
                                <input wire:model="fecha_nacimiento_solicitante" type="date"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_nacimiento_solicitante') border-red-500 @else border-gray-300 @enderror">
                                @error('fecha_nacimiento_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Género</label>
                                <select wire:model="genero_solicitante"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('genero_solicitante') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Selecciona una opción...</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                    <option value="otro">Otro</option>
                                </select>
                                @error('genero_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel Educativo</label>
                                <select wire:model="nivel_educativo_solicitante"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nivel_educativo_solicitante') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Selecciona una opción...</option>
                                    <option value="primaria">Primaria</option>
                                    <option value="secundaria">Secundaria</option>
                                    <option value="bachiller">Bachiller</option>
                                    <option value="tecnico">Técnico</option>
                                    <option value="tecnologo">Tecnólogo</option>
                                    <option value="universitario">Universitario</option>
                                    <option value="posgrado">Posgrado</option>
                                </select>
                                @error('nivel_educativo_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>
                    <!-- Motivación y Mensaje -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Motivación</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    ¿Por qué quieres tomar este curso? <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="motivacion"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('motivacion') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Selecciona una opción...</option>
                                    <option value="desarrollo_profesional">Desarrollo profesional</option>
                                    <option value="cambio_carrera">Cambio de carrera</option>
                                    <option value="emprendimiento">Emprendimiento</option>
                                    <option value="actualizacion">Actualización de conocimientos</option>
                                    <option value="interes_personal">Interés personal</option>
                                    <option value="otro">Otro</option>
                                </select>
                                @error('motivacion') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Mensaje adicional <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model="mensaje" rows="3"
                                          class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mensaje') border-red-500 @else border-gray-300 @enderror"
                                          placeholder="Cuéntanos por qué quieres tomar este curso..."></textarea>
                                @error('mensaje') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Información importante -->
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-800">
                                    <strong>Importante:</strong> Al enviar esta solicitud, un administrador revisará tus datos y si es aprobada, recibirás un correo electrónico con tus credenciales para acceder al sistema y al curso.
                                </p>
                            </div>
                        </div>
                    </div>
                    <!-- Botones de Acción -->
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button type="button"
                                wire:click="cerrarModal"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="button"
                                wire:click="enviarSolicitud"
                                wire:loading.attr="disabled"
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span wire:loading.remove>Enviar Solicitud</span>
                            <span wire:loading>
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
