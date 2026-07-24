<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Gestión de Certificados</h1>
                <p class="text-gray-600 mt-2">Sube y administra los certificados PDF de finalización de cursos</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('admin.dashboard') }}"
                   class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
                <button wire:click="abrirModalCarga()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Subir Nuevo Certificado
                </button>
            </div>
        </div>

        <!-- Mensajes de sesión -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Estudiante</label>
                    <select wire:model.live="filtroEstudiante"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los estudiantes</option>
                        @foreach($estudiantes as $est)
                            <option value="{{ $est->codigo }}">{{ $est->user->name }} {{ $est->user->apellido }} ({{ $est->codigo }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Filtrar por Curso</label>
                    <select wire:model.live="filtroCurso"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Todos los cursos</option>
                        @foreach($cursos as $curso)
                            <option value="{{ $curso->codigo }}">{{ $curso->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Lista de Certificados -->
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">
                    Certificados Subidos
                    <span class="text-sm font-normal text-gray-500 ml-2">({{ $certificados->count() }} total)</span>
                </h2>
            </div>
            <div class="p-6">
                @if($certificados && $certificados->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estudiante</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Certificado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Emisión</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descargas</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Archivo</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($certificados as $cert)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-xs font-medium">
                                                    {{ substr($cert->estudiante->user->name, 0, 1) }}{{ substr($cert->estudiante->user->apellido, 0, 1) }}
                                                </div>
                                                <div class="ml-3">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $cert->estudiante->user->name }} {{ $cert->estudiante->user->apellido }}
                                                    </div>
                                                    <div class="text-xs text-gray-500">{{ $cert->estudiante->codigo }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $cert->curso->nombre }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="text-sm font-mono text-gray-600">{{ $cert->numero_certificado }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $cert->fecha_emision ? $cert->fecha_emision->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $cert->descargas }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($cert->archivo_path)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    Subido
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                    Sin archivo
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <button wire:click="editarCertificado({{ $cert->id }})"
                                                        class="text-blue-600 hover:text-blue-900"
                                                        title="Cambiar archivo PDF">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                                    </svg>
                                                </button>
                                                <button wire:click="eliminarCertificado({{ $cert->id }})"
                                                        wire:confirm="¿Estás seguro de eliminar este certificado? Esta acción no se puede deshacer."
                                                        class="text-red-600 hover:text-red-900"
                                                        title="Eliminar certificado">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12">
                        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No hay certificados subidos</h3>
                        <p class="text-gray-500 mb-6">Comienza subiendo el primer certificado para tus estudiantes.</p>
                        <button wire:click="abrirModalCarga()"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Subir Primer Certificado
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal de Carga de Certificado -->
    @if($mostrarModalCarga)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
                <!-- Header del Modal -->
                <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">
                            {{ $certificadoEditandoId ? 'Actualizar Certificado' : 'Subir Nuevo Certificado' }}
                        </h2>
                        <p class="text-sm text-gray-600 mt-1">
                            {{ $certificadoEditandoId ? 'Selecciona un nuevo archivo PDF para reemplazar el existente.' : 'Selecciona el estudiante, curso y archivo PDF del certificado.' }}
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
                <form wire:submit="subirCertificado()" class="p-6 space-y-6">
                    <!-- Seleccionar Estudiante -->
                    <div>
                        <label for="estudianteSeleccionado" class="block text-sm font-medium text-gray-700 mb-2">
                            Estudiante <span class="text-red-500">*</span>
                        </label>
                        <select wire:model.live="estudianteSeleccionado"
                                id="estudianteSeleccionado"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('estudianteSeleccionado') border-red-500 @else border-gray-300 @enderror"
                                {{ $certificadoEditandoId ? 'disabled' : '' }}>
                            <option value="">Selecciona un estudiante</option>
                            @foreach($estudiantes as $est)
                                <option value="{{ $est->codigo }}">
                                    {{ $est->user->name }} {{ $est->user->apellido }} - {{ $est->codigo }} ({{ $est->user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('estudianteSeleccionado')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Seleccionar Curso -->
                    <div>
                        <label for="cursoSeleccionado" class="block text-sm font-medium text-gray-700 mb-2">
                            Curso <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="cursoSeleccionado"
                                id="cursoSeleccionado"
                                class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('cursoSeleccionado') border-red-500 @else border-gray-300 @enderror"
                                {{ $certificadoEditandoId ? 'disabled' : '' }}
                                {{ !$estudianteSeleccionado ? 'disabled' : '' }}>
                            <option value="">{{ $estudianteSeleccionado ? 'Selecciona un curso' : 'Primero selecciona un estudiante' }}</option>
                            @foreach($cursosEstudiante as $curso)
                                <option value="{{ $curso->codigo }}">{{ $curso->nombre }}</option>
                            @endforeach
                        </select>
                        @error('cursoSeleccionado')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subir Archivo PDF -->
                    <div>
                        <label for="archivoPDF" class="block text-sm font-medium text-gray-700 mb-2">
                            Archivo PDF del Certificado <span class="text-red-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-blue-500 transition-colors @error('archivoPDF') border-red-500 @enderror">
                            <div class="space-y-1 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <div class="flex text-sm text-gray-600">
                                    <label for="archivoPDF" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none">
                                        <span>Selecciona un archivo PDF</span>
                                        <input wire:model="archivoPDF"
                                               id="archivoPDF"
                                               type="file"
                                               accept=".pdf,application/pdf"
                                               class="sr-only">
                                    </label>
                                    <p class="pl-1">o arrastra y suelta</p>
                                </div>
                                <p class="text-xs text-gray-500">PDF hasta 10MB</p>
                            </div>
                        </div>
                        @error('archivoPDF')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                        @if($archivoPDF)
                            <div class="mt-2 flex items-center text-sm text-green-600">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                                {{ $archivoPDF->getClientOriginalName() }}
                            </div>
                        @endif
                    </div>

                    <!-- Información -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    El certificado será visible para el estudiante en su panel de "Mis Certificados" y podrá descargarlo.
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
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="flex-1 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                            <span wire:loading.remove>
                                {{ $certificadoEditandoId ? 'Actualizar Certificado' : 'Subir Certificado' }}
                            </span>
                            <span wire:loading>
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Subiendo...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
