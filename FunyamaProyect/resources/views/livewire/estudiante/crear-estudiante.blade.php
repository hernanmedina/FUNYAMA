{{-- resources/views/livewire/estudiante/crear-estudiante.blade.php --}}

<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Crear Nuevo Estudiante</h1>
                <p class="text-gray-600 mt-1">Registrar un nuevo estudiante en el sistema</p>
            </div>
            <a href="{{ route('admin.estudiantes.index') }}" 
               class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Estudiantes
            </a>
        </div>

        <!-- Mensajes -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @error('general')
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $message }}
            </div>
        @enderror

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow p-6">
            <form wire:submit="store">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Información de Usuario -->
                    <div class="md:col-span-2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información de Usuario</h3>
                    </div>

                    <!-- Nombre -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre *
                        </label>
                        <input type="text" 
                               id="name"
                               wire:model="name"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Apellido -->
                    <div>
                        <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">
                            Apellido
                        </label>
                        <input type="text" 
                               id="apellido"
                               wire:model="apellido"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('apellido') border-red-500 @enderror">
                        @error('apellido')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               id="email"
                               wire:model="email"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Contraseña *
                        </label>
                        <input type="password" 
                               id="password"
                               wire:model="password"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Teléfono -->
                    <div>
                        <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                            Teléfono
                        </label>
                        <input type="text" 
                               id="telefono"
                               wire:model="telefono"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('telefono') border-red-500 @enderror">
                        @error('telefono')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Información de Estudiante -->
                    <div class="md:col-span-2 mt-4">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Información Académica</h3>
                    </div>

                    <!-- Código Curso -->
                    <div>
                        <label for="codigo" class="block text-sm font-medium text-gray-700 mb-2">
                            Código Curso *
                        </label>
                        <input type="text" 
                               id="codigo"
                               wire:model="codigo"
                               placeholder="Ej: EST-2025-001"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo') border-red-500 @enderror">
                        @error('codigo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Fecha de Nacimiento -->
                    <div>
                        <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Nacimiento
                        </label>
                        <input type="date" 
                               id="fecha_nacimiento"
                               wire:model="fecha_nacimiento"
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('fecha_nacimiento') border-red-500 @enderror">
                        @error('fecha_nacimiento')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Género -->
                    <div>
                        <label for="genero" class="block text-sm font-medium text-gray-700 mb-2">
                            Género
                        </label>
                        <select id="genero"
                                wire:model="genero"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('genero') border-red-500 @enderror">
                            <option value="">Seleccionar...</option>
                            <option value="masculino">Masculino</option>
                            <option value="femenino">Femenino</option>
                            <option value="otro">Otro</option>
                        </select>
                        @error('genero')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nivel Educativo -->
                    <div>
                        <label for="nivel_educativo" class="block text-sm font-medium text-gray-700 mb-2">
                            Nivel Educativo
                        </label>
                        <input type="text" 
                               id="nivel_educativo"
                               wire:model="nivel_educativo"
                               placeholder="Ej: Secundaria, Universidad, etc."
                               class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('nivel_educativo') border-red-500 @enderror">
                        @error('nivel_educativo')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Intereses -->
                    <div class="md:col-span-2">
                        <label for="intereses" class="block text-sm font-medium text-gray-700 mb-2">
                            Intereses
                        </label>
                        <textarea id="intereses"
                                  wire:model="intereses"
                                  rows="3"
                                  placeholder="Intereses académicos o personales del estudiante..."
                                  class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('intereses') border-red-500 @enderror"></textarea>
                        @error('intereses')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:model="activo"
                                   class="rounded border-gray-300 text-blue-600 focus:ring-blue-500 mr-2">
                            <span class="text-sm font-medium text-gray-700">Estudiante Activo</span>
                        </label>
                    </div>

                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.estudiantes.index') }}" 
                       class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition duration-200 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear Estudiante
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>