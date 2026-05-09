<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $estudiante->user->name }} {{ $estudiante->user->apellido ?? '' }}</h1>
                <p class="text-gray-600 mt-1">Código: {{ $estudiante->codigo }}</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.estudiantes.edit', $estudiante->codigo) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar
                </a>
                <a href="{{ route('admin.estudiantes.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Información Personal -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Información Personal</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                            <p class="text-gray-900 mt-1">{{ $estudiante->user->name }} {{ $estudiante->user->apellido ?? '' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Código</label>
                            <p class="text-gray-900 mt-1">{{ $estudiante->codigo }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <p class="text-gray-900 mt-1">{{ $estudiante->user->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Teléfono</label>
                            <p class="text-gray-900 mt-1">{{ $estudiante->user->telefono ?? 'No especificado' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Fecha de Nacimiento</label>
                            <p class="text-gray-900 mt-1">{{ optional($estudiante->fecha_nacimiento)->format('d/m/Y') ?? 'No especificada' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Género</label>
                            <p class="text-gray-900 mt-1">{{ $estudiante->genero ?? 'No especificado' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Cursos Inscritos -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Cursos Inscritos</h2>
                    @if($estudiante->cursos()->exists())
                        <div class="space-y-3">
                            @foreach($estudiante->cursos as $curso)
                                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ $curso->nombre }}</p>
                                        <p class="text-sm text-gray-600">Código: {{ $curso->codigo }}</p>
                                    </div>
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                                        {{ ucfirst($curso->pivot->estado ?? 'inscrito') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600">No tiene cursos inscritos.</p>
                    @endif
                </div>
            </div>

            <!-- Estadísticas -->
            <div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Estadísticas</h2>
                    <div class="space-y-4">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-blue-600 font-medium">Cursos Totales</p>
                            <p class="text-2xl font-bold text-blue-900">{{ $estudiante->cursos()->count() }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-green-600 font-medium">Cursos Completados</p>
                            <p class="text-2xl font-bold text-green-900">{{ $estudiante->cursos()->wherePivot('estado', 'completado')->count() }}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded-lg">
                            <p class="text-sm text-yellow-600 font-medium">En Progreso</p>
                            <p class="text-2xl font-bold text-yellow-900">{{ $estudiante->cursos()->wherePivot('estado', 'en_progreso')->count() }}</p>
                        </div>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            <p class="text-sm text-purple-600 font-medium">Certificados</p>
                            <p class="text-2xl font-bold text-purple-900">{{ $estudiante->certificados()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
