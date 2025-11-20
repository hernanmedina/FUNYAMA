<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Editar Curso: {{ $curso->nombre }}</h1>
                <p class="text-gray-600 mt-1">Actualiza la información del curso</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.cursos.show', $curso->idCurso) }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver Detalles
                </a>
                <a href="{{ route('admin.cursos.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <!-- Estadísticas rápidas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-blue-50 rounded-lg p-4">
                <div class="text-blue-600 text-sm font-medium">Estudiantes Inscritos</div>
                <div class="text-2xl font-bold text-blue-700">{{ $inscritos }}</div>
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

        <!-- Formulario -->
        <form wire:submit="actualizarCurso" class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Columna 1 - Información Básica -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Información Básica -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información Básica</h2>

                            <!-- Nombre -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del Curso *</label>
                                <input type="text"
                                       wire:model="nombre"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('nombre') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción *</label>
                                <textarea wire:model="descripcion"
                                          rows="4"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                @error('descripcion') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Cronograma -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cronograma *</label>
                                <textarea wire:model="cronograma"
                                          rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                @error('cronograma') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Requisitos -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Requisitos *</label>
                                <textarea wire:model="requisitos"
                                          rows="3"
                                          class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                @error('requisitos') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Detalles del Curso -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Detalles del Curso</h2>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Objetivos -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Objetivos de Aprendizaje</label>
                                    <textarea wire:model="objetivos"
                                              rows="3"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    @error('objetivos') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>

                                <!-- Materiales Incluidos -->
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Materiales Incluidos</label>
                                    <textarea wire:model="materiales_incluidos"
                                              rows="3"
                                              class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                                    @error('materiales_incluidos') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna 2 - Configuración -->
                    <div class="space-y-6">
                        <!-- Imagen del Curso -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Imagen del Curso</h2>

                            @if($imagen_portada)
                                <div class="mb-4">
                                    <img src="{{ $imagen_portada->temporaryUrl() }}"
                                         alt="Vista previa"
                                         class="w-full h-48 object-cover rounded-lg">
                                    <p class="text-sm text-gray-500 mt-2 text-center">Nueva imagen</p>
                                </div>
                            @elseif($imagen_portada_temp)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $imagen_portada_temp) }}"
                                         alt="{{ $curso->nombre }}"
                                         class="w-full h-48 object-cover rounded-lg">
                                    <p class="text-sm text-gray-500 mt-2 text-center">Imagen actual</p>
                                </div>
                            @endif

                            <div class="flex items-center justify-center w-full">
                                <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Click para cambiar</span></p>
                                        <p class="text-xs text-gray-500">PNG, JPG, JPEG (MAX. 2MB)</p>
                                    </div>
                                    <input type="file"
                                           wire:model="imagen_portada"
                                           class="hidden"
                                           accept="image/*">
                                </label>
                            </div>
                            @error('imagen_portada') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Configuración -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Configuración</h2>

                            <!-- Cupo Total -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cupo Total *</label>
                                <input type="number"
                                       wire:model="cupo_total"
                                       min="{{ $inscritos }}"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">Mínimo: {{ $inscritos }} (estudiantes inscritos)</p>
                                @error('cupo_total') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Duración -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración (horas)</label>
                                    <input type="number"
                                           wire:model="duracion_horas"
                                           min="1"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('duracion_horas') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Duración (texto)</label>
                                    <input type="text"
                                           wire:model="duracion_texto"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    @error('duracion_texto') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <!-- Nivel -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nivel *</label>
                                <select wire:model="nivel"
                                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <option value="principiante">Principiante</option>
                                    <option value="intermedio">Intermedio</option>
                                    <option value="avanzado">Avanzado</option>
                                </select>
                                @error('nivel') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Fecha Inicio -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de Inicio</label>
                                <input type="date"
                                       wire:model="fecha_inicio"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('fecha_inicio') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Precios -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Precios</h2>

                            <!-- Precio Regular -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio Regular *</label>
                                <input type="number"
                                       wire:model="precio_regular"
                                       step="0.01"
                                       min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('precio_regular') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Precio Descuento -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Precio con Descuento</label>
                                <input type="number"
                                       wire:model="precio_descuento"
                                       step="0.01"
                                       min="0"
                                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('precio_descuento') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- Estado -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h2 class="text-lg font-semibold text-gray-800 mb-4">Estado</h2>

                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model="publicado"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">Publicar curso</span>
                                </label>

                                <label class="flex items-center">
                                    <input type="checkbox"
                                           wire:model="destacado"
                                           class="rounded border-gray-300 text-yellow-600 focus:ring-yellow-500">
                                    <span class="ml-2 text-sm text-gray-700">Marcar como destacado</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.cursos.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition duration-200 flex items-center">
                        <svg wire:loading class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Actualizar Curso
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
