<div>
    <div class="container mx-auto py-6 px-4">
        <div class="mb-6">
            <a href="{{ route('admin.eventos.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                &larr; Volver a eventos
            </a>
            <h1 class="text-3xl font-bold text-gray-800 mt-2">Crear Nuevo Evento</h1>
        </div>

        <form wire:submit="crearEvento" class="bg-white rounded-lg shadow p-6 max-w-4xl">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Título -->
                <div class="md:col-span-2">
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">
                        Título <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="titulo" type="text" id="titulo" placeholder="Ingresa el título del evento"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('titulo') border-red-500 @enderror">
                    @error('titulo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <textarea wire:model="descripcion" id="descripcion" rows="3" placeholder="Descripción breve del evento"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"></textarea>
                    @error('descripcion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Contenido -->
                <div class="md:col-span-2">
                    <label for="contenido" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenido Detallado
                    </label>
                    <textarea wire:model="contenido" id="contenido" rows="4" placeholder="Información completa del evento"
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('contenido') border-red-500 @enderror"></textarea>
                    @error('contenido') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Fecha -->
                <div>
                    <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="fecha" type="date" id="fecha"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha') border-red-500 @enderror">
                    @error('fecha') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hora Inicio -->
                <div>
                    <label for="hora_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora Inicio <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="hora_inicio" type="time" id="hora_inicio"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hora_inicio') border-red-500 @enderror">
                    @error('hora_inicio') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Hora Fin -->
                <div>
                    <label for="hora_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        Hora Fin <span class="text-red-500">*</span>
                    </label>
                    <input wire:model="hora_fin" type="time" id="hora_fin"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('hora_fin') border-red-500 @enderror">
                    @error('hora_fin') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Tipo de Evento -->
                <div>
                    <label for="tipo_evento" class="block text-sm font-medium text-gray-700 mb-2">
                        Tipo de Evento <span class="text-red-500">*</span>
                    </label>
                    <select wire:model="tipo_evento" id="tipo_evento"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('tipo_evento') border-red-500 @enderror">
                        <option value="presencial">Presencial</option>
                        <option value="virtual">Virtual</option>
                        <option value="hibrido">Híbrido</option>
                    </select>
                    @error('tipo_evento') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Ubicación -->
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-2">
                        Ubicación
                    </label>
                    <input wire:model="ubicacion" type="text" id="ubicacion" placeholder="Lugar del evento"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ubicacion') border-red-500 @enderror">
                    @error('ubicacion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Dirección -->
                <div>
                    <label for="direccion" class="block text-sm font-medium text-gray-700 mb-2">
                        Dirección
                    </label>
                    <input wire:model="direccion" type="text" id="direccion" placeholder="Dirección completa"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('direccion') border-red-500 @enderror">
                    @error('direccion') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Ciudad -->
                <div>
                    <label for="ciudad" class="block text-sm font-medium text-gray-700 mb-2">
                        Ciudad
                    </label>
                    <input wire:model="ciudad" type="text" id="ciudad" placeholder="Ciudad"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('ciudad') border-red-500 @enderror">
                    @error('ciudad') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Enlace Virtual -->
                <div>
                    <label for="enlace_virtual" class="block text-sm font-medium text-gray-700 mb-2">
                        Enlace Virtual (Zoom, Meet, etc)
                    </label>
                    <input wire:model="enlace_virtual" type="url" id="enlace_virtual" placeholder="https://zoom.us/..."
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('enlace_virtual') border-red-500 @enderror">
                    @error('enlace_virtual') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Cupo Máximo -->
                <div>
                    <label for="cupo_maximo" class="block text-sm font-medium text-gray-700 mb-2">
                        Cupo Máximo
                    </label>
                    <input wire:model="cupo_maximo" type="number" id="cupo_maximo" placeholder="Cantidad de participantes"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cupo_maximo') border-red-500 @enderror">
                    @error('cupo_maximo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Costo -->
                <div>
                    <label for="costo" class="block text-sm font-medium text-gray-700 mb-2">
                        Costo (si aplica)
                    </label>
                    <input wire:model="costo" type="number" id="costo" placeholder="0.00" step="0.01"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('costo') border-red-500 @enderror">
                    @error('costo') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Imagen -->
                <div class="md:col-span-2">
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen del Evento
                    </label>
                    <input wire:model="imagen" type="file" id="imagen" accept="image/*"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('imagen') border-red-500 @enderror">
                    @error('imagen') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                    @if($imagen)
                        <p class="text-sm text-green-600 mt-2">Imagen cargada</p>
                    @endif
                </div>

                <!-- Checkboxes -->
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input wire:model="publicado" type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="ml-2 text-sm text-gray-700">Publicar evento</span>
                        </label>
                        <label class="flex items-center">
                            <input wire:model="destacado" type="checkbox" class="w-4 h-4 text-yellow-600 rounded">
                            <span class="ml-2 text-sm text-gray-700">Destacar evento</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Crear Evento
                </button>
                <a href="{{ route('admin.eventos.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-lg font-medium transition-colors">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
