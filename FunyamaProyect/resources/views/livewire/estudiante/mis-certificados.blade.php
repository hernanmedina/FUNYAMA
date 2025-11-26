<div class="container mx-auto py-6 px-4">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Mis Certificados</h1>
        <p class="text-gray-600 mt-2">Descarga los certificados de los cursos que has completado</p>
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

    <!-- Certificados -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($certificados as $cert)
            <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow p-6">
                <!-- Icono de certificado -->
                <div class="flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <!-- Información del certificado -->
                <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">{{ $cert->curso->nombre }}</h3>
                
                <div class="space-y-3 mb-4 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Número:</span>
                        <span class="font-mono text-gray-900">{{ $cert->numero_certificado }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Fecha de Emisión:</span>
                        <span class="text-gray-900">{{ $cert->fecha_emision->format('d/m/Y') }}</span>
                    </div>
                    @if($cert->calificacion_final)
                        <div class="flex justify-between">
                            <span>Calificación:</span>
                            <span class="font-semibold text-gray-900">{{ $cert->calificacion_final }}/10</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span>Descargas:</span>
                        <span class="text-gray-900">{{ $cert->descargas }}</span>
                    </div>
                </div>

                <!-- Estado de pago -->
                <div class="mb-4 inline-block px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ✓ Completado
                </div>

                <!-- Botones -->
                <div class="flex space-x-2">
                    <button wire:click="descargarCertificado({{ $cert->id }})"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2 rounded transition-colors flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Descargar
                    </button>
                    <a href="{{ route('admin.estudiantes.show', Auth::user()->estudiante->idEstudiante) }}"
                       class="px-4 py-2 border border-gray-300 rounded hover:bg-gray-50 transition-colors flex items-center justify-center"
                       title="Ver detalles">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </a>
                </div>
            </div>
        @empty
            <!-- Estado vacío -->
            <div class="col-span-full">
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No hay certificados disponibles</h3>
                    <p class="text-gray-500 mb-6">Completa cursos para obtener certificados</p>
                    <a href="{{ route('cursos.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Ver Cursos Disponibles
                    </a>
                </div>
            </div>
        @endforelse
    </div>
</div>
