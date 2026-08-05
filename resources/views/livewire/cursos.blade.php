<div class="w-full">
    <!-- Encabezado con búsqueda -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <h2 class="text-xl font-semibold text-gray-800">
            {{ $cursos->total() }} curso(s) disponible(s)
        </h2>
        <div class="relative w-full sm:w-72">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input wire:model.live.debounce.300ms="search"
                   type="text" placeholder="Buscar cursos..."
                   class="w-full pl-9 pr-10 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white shadow-sm">
            @if($search)
                <button wire:click="$set('search', '')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    @if($cursos->count() > 0)
        <!-- Grid container -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($cursos as $curso)
                <div class="w-full flex">
                    <x-curso-card :curso="$curso" :esta-inscrito="in_array($curso->codigo, $cursosInscritosIds ?? [])" />
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($cursos->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $cursos->links() }}
            </div>
        @endif
    @else
        <!-- Estado vacío -->
        <div class="text-center py-16">
            <svg class="w-20 h-20 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No se encontraron cursos</h3>
            <p class="text-gray-400">Intenta con otros términos de búsqueda.</p>
        </div>
    @endif

    <!-- Modal de Solicitud de Inscripción -->
    @if($mostrarModalSolicitud && $cursoSeleccionado)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header del Modal -->
                <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Solicitar Inscripción</h2>
                        <p class="text-sm text-gray-600 mt-1">
                            Curso: <span class="font-semibold">{{ $cursoSeleccionado->nombre }}</span>
                        </p>
                    </div>
                    <button wire:click="cerrarModal()"
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
                                <p class="font-medium text-gray-900">{{ $cursoSeleccionado->nombre }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Precio:</p>
                                <p class="font-medium text-gray-900">${{ number_format($cursoSeleccionado->precioFinal, 2) }} COP</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Duración:</p>
                                <p class="font-medium text-gray-900">{{ $cursoSeleccionado->duracion_texto ?? 'Flexible' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Cupos disponibles:</p>
                                <p class="font-medium text-gray-900">{{ $cursoSeleccionado->cupo_disponible }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- INFORMACIÓN PERSONAL DEL SOLICITANTE -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Tus Datos Personales</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Nombre -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nombre <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="nombre_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nombre_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu nombre">
                                @error('nombre_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Apellido -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Apellido
                                </label>
                                <input wire:model="apellido_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('apellido_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu apellido">
                                @error('apellido_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Correo Electrónico <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="email_solicitante" type="email"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="correo@ejemplo.com">
                                @error('email_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Documento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Documento de Identidad <span class="text-red-500">*</span>
                                </label>
                                <input wire:model="documento_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('documento_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Cédula, NIT, etc.">
                                @error('documento_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Teléfono -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Teléfono
                                </label>
                                <input wire:model="telefono_solicitante" type="tel"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefono_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu número de contacto">
                                @error('telefono_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Dirección -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Dirección
                                </label>
                                <input wire:model="direccion_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Tu dirección">
                                @error('direccion_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Fecha de Nacimiento -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Fecha de Nacimiento
                                </label>
                                <input wire:model="fecha_nacimiento_solicitante" type="date"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_nacimiento_solicitante') border-red-500 @else border-gray-300 @enderror">
                                @error('fecha_nacimiento_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Género -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Género
                                </label>
                                <select wire:model="genero_solicitante"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('genero_solicitante') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Seleccionar...</option>
                                    <option value="masculino">Masculino</option>
                                    <option value="femenino">Femenino</option>
                                    <option value="otro">Otro</option>
                                </select>
                                @error('genero_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            <!-- Nivel Educativo -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Nivel Educativo
                                </label>
                                <input wire:model="nivel_educativo_solicitante" type="text"
                                       class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nivel_educativo_solicitante') border-red-500 @else border-gray-300 @enderror"
                                       placeholder="Ej: Secundaria, Universidad, Técnico, etc.">
                                @error('nivel_educativo_solicitante') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- MOTIVACIÓN -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información de la Solicitud</h3>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    ¿Cuál es tu motivación para tomar este curso? <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="motivacion"
                                        class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('motivacion') border-red-500 @else border-gray-300 @enderror">
                                    <option value="">Selecciona una opción</option>
                                    <option value="interes_personal">Interés personal</option>
                                    <option value="desarrollo_profesional">Desarrollo profesional</option>
                                    <option value="requisito_laboral">Requisito laboral</option>
                                    <option value="certificacion">Obtener certificación</option>
                                    <option value="actualizacion">Actualización de conocimientos</option>
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
                                wire:click="cerrarModal()"
                                class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            Cancelar
                        </button>
                        <button type="button"
                                wire:click="enviarSolicitud()"
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
