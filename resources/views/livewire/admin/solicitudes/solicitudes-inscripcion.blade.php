<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Solicitudes de Inscripción</h1>
                <p class="text-gray-600 mt-1">Gestiona las solicitudes de inscripción a cursos enviadas por los visitantes</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.dashboard') }}"
                   class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Volver al Dashboard
                </a>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-between">
                <!-- Filtro por estado -->
                <div class="flex items-center space-x-2">
                    <span class="text-sm font-medium text-gray-700">Estado:</span>
                    <select wire:model="filtroEstado"
                            class="border border-gray-300 rounded-lg px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="pendiente">Pendientes</option>
                        <option value="en_proceso">En Proceso</option>
                        <option value="resuelta">Resueltas</option>
                        <option value="cancelada">Canceladas</option>
                        <option value="todas">Todas</option>
                    </select>
                </div>

                <!-- Búsqueda -->
                <div class="flex-1 max-w-md">
                    <div class="relative">
                        <input wire:model="search" type="text"
                               placeholder="Buscar solicitudes..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mensajes de sesión -->
        @if (session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('message') }}
            </div>
        @endif

        <!-- Tabla de solicitudes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Solicitante</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Curso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->solicitudes as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 font-medium">
                                            {{ substr($item->datos_adicionales['nombre'] ?? $item->email_contacto, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $item->datos_adicionales['nombre'] ?? 'Sin nombre' }}
                                            {{ $item->datos_adicionales['apellido'] ?? '' }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ $item->email_contacto }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $item->datos_adicionales['nombre_curso'] ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $estadoColors = [
                                        'pendiente' => 'bg-yellow-100 text-yellow-800',
                                        'en_proceso' => 'bg-blue-100 text-blue-800',
                                        'resuelta' => 'bg-green-100 text-green-800',
                                        'cancelada' => 'bg-red-100 text-red-800',
                                    ];
                                    $estadoTextos = [
                                        'pendiente' => 'Pendiente',
                                        'en_proceso' => 'En Proceso',
                                        'resuelta' => 'Aprobada',
                                        'cancelada' => 'Rechazada',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $estadoColors[$item->estado] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $estadoTextos[$item->estado] ?? $item->estado }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $item->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($item->estado === 'pendiente' || $item->estado === 'en_proceso')
                                    <button wire:click="abrirModalRevision({{ $item->idSolicitud }})"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                        Revisar
                                    </button>
                                    @if($item->estado === 'pendiente')
                                        <button wire:click="marcarEnProceso({{ $item->idSolicitud }})"
                                                class="text-yellow-600 hover:text-yellow-900">
                                            En Proceso
                                        </button>
                                    @endif
                                @else
                                    <span class="text-gray-400">Completada</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="text-lg font-medium">No hay solicitudes {{ $filtroEstado !== 'todas' ? $filtroEstado : '' }}</p>
                                <p class="mt-1">Cuando los visitantes soliciten inscripción a cursos, aparecerán aquí.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="mt-4">
            {{ $this->solicitudes->links() }}
        </div>
    </div>

    <!-- Modal de Revisión -->
    @if($mostrarModalRevision && $solicitudSeleccionada)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-3xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Header -->
                <div class="sticky top-0 flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-white">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">Revisar Solicitud</h2>
                        <p class="text-sm text-gray-600 mt-1">Revisa los datos y decide si apruebas o rechazas la solicitud</p>
                    </div>
                    <button wire:click="cerrarModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Error general -->
                    @error('general')
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            {{ $message }}
                        </div>
                    @enderror

                    <!-- Información de la solicitud -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-900 mb-3">Datos de la Solicitud</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Curso solicitado:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['nombre_curso'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Código del curso:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['codigo_curso'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Motivación:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['motivo_inscripcion'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Fecha de solicitud:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="mt-3">
                            <p class="text-gray-500">Mensaje del solicitante:</p>
                            <p class="text-gray-700 bg-white rounded p-2 mt-1">{{ $solicitudSeleccionada->mensaje }}</p>
                        </div>
                    </div>

                    <!-- Datos Personales del Solicitante -->
                    <div>
                        <h3 class="font-semibold text-gray-900 mb-3">Datos Personales</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">Nombre completo:</p>
                                <p class="font-medium">
                                    {{ $solicitudSeleccionada->datos_adicionales['nombre'] ?? '' }}
                                    {{ $solicitudSeleccionada->datos_adicionales['apellido'] ?? '' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-gray-500">Email:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->email_contacto }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Documento:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['documento_ID'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Teléfono:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->telefono ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Dirección:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['direccion'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Fecha Nacimiento:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['fecha_nacimiento'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Género:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['genero'] ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-500">Nivel Educativo:</p>
                                <p class="font-medium">{{ $solicitudSeleccionada->datos_adicionales['nivel_educativo'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Código de Estudiante -->
                    @if($solicitudSeleccionada->estado === 'pendiente' || $solicitudSeleccionada->estado === 'en_proceso')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Código de Estudiante (generado automáticamente)
                            </label>
                            <input wire:model="codigo_generado" type="text"
                                   class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('codigo_generado') border-red-500 @else border-gray-300 @enderror">
                            @error('codigo_generado') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            <p class="mt-1 text-xs text-gray-500">Puedes modificarlo si lo deseas. Debe ser único.</p>
                        </div>
                    @endif

                    <!-- Respuesta -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Mensaje de respuesta
                            @if($solicitudSeleccionada->estado !== 'pendiente' && $solicitudSeleccionada->estado !== 'en_proceso')
                                <span class="text-red-500">* (Requerido para rechazar)</span>
                            @endif
                        </label>
                        <textarea wire:model="respuesta" rows="3"
                                  class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('respuesta') border-red-500 @else border-gray-300 @enderror"
                                  placeholder="Escribe un mensaje para el solicitante..."></textarea>
                        @error('respuesta') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    @if($solicitudSeleccionada->estado === 'pendiente' || $solicitudSeleccionada->estado === 'en_proceso')
                        <!-- Botones de Acción -->
                        <div class="flex gap-3 pt-4 border-t border-gray-200">
                            <button type="button"
                                    wire:click="rechazarSolicitud()"
                                    wire:confirm="¿Estás seguro de rechazar esta solicitud? Se le notificará al solicitante."
                                    class="flex-1 px-4 py-2 border border-red-300 text-red-700 rounded-lg font-medium hover:bg-red-50 transition-colors">
                                Rechazar Solicitud
                            </button>
                            <button type="button"
                                    wire:click="aprobarSolicitud()"
                                    wire:loading.attr="disabled"
                                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                                <span wire:loading.remove>
                                    <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Aprobar y Crear Estudiante
                                </span>
                                <span wire:loading>
                                    <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Procesando...
                                </span>
                            </button>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <p class="text-gray-600">Esta solicitud ya fue {{ $solicitudSeleccionada->estado === 'resuelta' ? 'aprobada' : 'rechazada' }}.</p>
                            @if($solicitudSeleccionada->respuesta)
                                <p class="text-sm text-gray-500 mt-2">Respuesta: {{ $solicitudSeleccionada->respuesta }}</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
