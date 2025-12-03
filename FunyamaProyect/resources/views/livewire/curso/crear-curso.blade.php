<div class="container mx-auto py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold mb-6">Crear Nuevo Curso</h1>

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <p class="text-red-700 font-semibold">Por favor corrige los errores:</p>
                <ul class="list-disc list-inside text-red-600 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form wire:submit="store" class="space-y-6 bg-white p-6 rounded-lg shadow">
            <!-- Código del Curso -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Código del Curso *</label>
                <input type="text" wire:model="codigo" placeholder="Ej: CUR-001" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('codigo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Nombre -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre del Curso *</label>
                <input type="text" wire:model="nombre" placeholder="Ej: Laravel Avanzado" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Slug -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Slug *</label>
                <input type="text" wire:model="slug" placeholder="Ej: laravel-avanzado" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                @error('slug') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Descripción -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción *</label>
                <textarea wire:model="descripcion" rows="3" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Cronograma -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Cronograma *</label>
                <textarea wire:model="cronograma" rows="2" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                @error('cronograma') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Requisitos -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Requisitos *</label>
                <textarea wire:model="requisitos" rows="2" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                @error('requisitos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Objetivos -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Objetivos</label>
                <textarea wire:model="objetivos" rows="2" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                @error('objetivos') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Duración -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración (horas)</label>
                    <input type="number" wire:model="duracion_horas" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('duracion_horas') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Duración (texto)</label>
                    <input type="text" wire:model="duracion_texto" placeholder="Ej: 4 semanas" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('duracion_texto') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Precios -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio Regular *</label>
                    <input type="number" step="0.01" wire:model="precio_regular" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('precio_regular') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Precio Descuento</label>
                    <input type="number" step="0.01" wire:model="precio_descuento" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('precio_descuento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Cupos -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cupo Total *</label>
                    <input type="number" wire:model="cupo_total" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('cupo_total') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cupo Disponible *</label>
                    <input type="number" wire:model="cupo_disponible" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('cupo_disponible') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Nivel -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Nivel</label>
                <select wire:model="nivel" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="principiante">Principiante</option>
                    <option value="intermedio">Intermedio</option>
                    <option value="avanzado">Avanzado</option>
                </select>
                @error('nivel') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Inicio</label>
                    <input type="date" wire:model="fecha_inicio" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('fecha_inicio') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Fecha Fin</label>
                    <input type="date" wire:model="fecha_fin" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('fecha_fin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Checkboxes -->
            <div class="flex gap-4">
                <label class="flex items-center">
                    <input type="checkbox" wire:model="publicado" class="rounded border-gray-300">
                    <span class="ml-2 text-gray-700">Publicado</span>
                </label>
                <label class="flex items-center">
                    <input type="checkbox" wire:model="destacado" class="rounded border-gray-300">
                    <span class="ml-2 text-gray-700">Destacado</span>
                </label>
            </div>

            <!-- Botones -->
            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Crear Curso
                </button>
                <a href="{{ route('admin.cursos.index') }}" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
