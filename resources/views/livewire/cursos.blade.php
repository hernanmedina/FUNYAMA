<div class="flex flex-col items-center gap-6 w-full">

    @foreach($cursos as $curso)
        <div class="w-full max-w-3xl">
            <x-curso-card
                :curso="$curso"
            />
        </div>
    @endforeach

</div>

<!-- Modal de Solicitud de Inscripción -->
@if($mostrarModalSolicitud && $cursoSeleccionado)
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Header del Modal -->
            <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Solicitar Inscripción</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Curso: <span class="font-mono">{{ $cursoSeleccionado->nombre }}</span>
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

                <!-- Motivación -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        ¿Cuál es tu motivación para tomar este curso? <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="motivacion"
                            class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent border-gray-300">
                        <option value="">Selecciona una opción</option>
                        <option value="interes_personal">Interés personal</option>
                        <option value="desarrollo_profesional">Desarrollo profesional</option>
                        <option value="requisito_laboral">Requisito laboral</option>
                        <option value="certificacion">Obtener certificación</option>
                        <option value="actualizacion">Actualización de conocimientos</option>
                        <option value="otro">Otro</option>
                    </select>
                    @error('motivacion')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Mensaje -->
                <div>
                    <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">
                        Mensaje adicional <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="mensaje"
                              id="mensaje"
                              rows="4"
                              class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('mensaje') border-red-500 @else border-gray-300 @enderror"
                              placeholder="Cuéntanos por qué quieres tomar este curso..."></textarea>
                    @error('mensaje')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres.</p>
                </div>

                <!-- Teléfono opcional -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono de contacto (opcional)
                    </label>
                    <input wire:model="telefono"
                           id="telefono"
                           type="tel"
                           class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent border-gray-300"
                           placeholder="Tu número de teléfono">
                    @error('telefono')
                        <p class="mt-2 text-sm text-red-500 font-medium">{{ $message }}</p>
                    @enderror
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
                                Tu solicitud será revisada por un administrador. Recibirás una respuesta por email.
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
