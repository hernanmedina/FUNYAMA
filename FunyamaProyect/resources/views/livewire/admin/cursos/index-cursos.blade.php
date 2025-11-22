<div>
    <div class="container mx-auto py-6 px-4">
        <!-- Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Gestión de Cursos</h1>
                <p class="text-gray-600 mt-1">Administra todos los cursos de la plataforma</p>
            </div>
            <div class="flex space-x-3 mt-4 md:mt-0">
                @if(!empty($selected) && count($selected) > 0)
                    <button wire:click="bulkDelete"
                            wire:confirm="¿Estás seguro de eliminar los cursos seleccionados?"
                            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                        Eliminar Seleccionados ({{ count($selected) }})
                    </button>
                @endif
                <a href="{{ route('admin.cursos.create') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nuevo Curso
                </a>
                <a href="{{ route('admin.cursos.eliminados') }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-4 py-2 rounded-lg flex items-center border border-gray-200">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h4l3 8 4-16 3 8h4"/>
                    </svg>
                    Cursos eliminados
                </a>
            </div>
        </div>

        <!-- Filtros y Búsqueda -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text"
                           wire:model.live="search"
                           placeholder="Buscar cursos por nombre o descripción..."
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                </div>
                <div class="flex space-x-2">
                    <select wire:model.live="perPage" class="border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="5">5 por página</option>
                        <option value="10">10 por página</option>
                        <option value="25">25 por página</option>
                        <option value="50">50 por página</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Tabla de Cursos -->

{{--        inicio de la tabla--}}
        <!-- LISTADO DE CURSOS: VISTA RESPONSIVA (Tabla en desktop + Cards en mobile) -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            @if($cursos->count() > 0)
                <!-- ---------- Desktop: Tabla (oculta en pantallas pequeñas) ---------- -->
                <div class="hidden sm:block overflow-x-auto">
                    <table class="min-w-full table-fixed divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="w-24 px-6 py-8"></th>

                            <th class="w-64 px-10 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                                wire:click="sortBy('nombre')">
                                Nombre
                            </th>

                            <th class="w-24 px-6 py-8"></th>

                            <th class="w-36 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cupos
                            </th>

                            <th class="w-36 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Precio
                            </th>

                            <th class="w-44 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estado
                            </th>

                            <th class="w-36 px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fecha Inicio
                            </th>

                            <th class="w-36 px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Acciones
                            </th>
                        </tr>
                        </thead>


                        <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($cursos as $curso)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <input type="checkbox" wire:model.live="selected" value="{{ $curso->idCurso }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>

                                <!-- Imagen + nombre: imagen con ancho fijo para separar texto a la derecha -->
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 mr-4">
                                            @if($curso->imagen_portada)
                                                <img src="{{ asset('storage/' . $curso->imagen_portada) }}"
                                                     alt="{{ $curso->nombre }}"
                                                     class="h-12 w-12 rounded-lg object-cover border border-gray-200"
                                                     style="width:48px; height:48px;">
                                            @else
                                                <div class="h-12 w-12 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $curso->nombre }}</div>
                                            <div class="text-xs text-gray-500 truncate">{{ Str::limit($curso->descripcion, 60) }}</div>
                                        </div>
                                    </div>
                                </td>

                                <td>
                                    <div>

                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        {{ $curso->cupo_disponible }} / {{ $curso->cupo_total }}
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-blue-600 h-2 rounded-full"
                                             style="width: {{ $curso->cupo_total > 0 ? (100 - ($curso->cupo_disponible / $curso->cupo_total * 100)) : 0 }}%"></div>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">${{ number_format($curso->precioFinal, 2) }}</div>
                                    @if($curso->precio_descuento)
                                        <div class="text-xs text-red-600 line-through">${{ number_format($curso->precio_regular, 2) }}</div>
                                    @endif
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex items-center">
                                            <span class="inline-block h-2 w-2 rounded-full {{ $curso->publicado ? 'bg-green-500' : 'bg-gray-300' }} mr-2"></span>
                                            <span class="text-sm {{ $curso->publicado ? 'text-green-600' : 'text-gray-500' }}">{{ $curso->publicado ? 'Publicado' : 'Oculto' }}</span>
                                        </div>
                                        @if($curso->destacado)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Destacado</span>
                                        @endif
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $curso->fecha_inicio ? $curso->fecha_inicio->format('d/m/Y') : 'No definida' }}
                                </td>

                                <td class="px-6 py-4 text-center text-sm font-medium">
                                    <div class="flex items-center justify-center space-x-3">
                                        <a href="{{ route('admin.cursos.show', $curso->idCurso) }}" class="text-blue-600 hover:text-blue-900" title="Ver">
                                            <!-- ojo: iconos pequeños y con padding para no pegar -->
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </a>
                                        <a href="{{ route('admin.cursos.edit', $curso->idCurso) }}" class="text-green-600 hover:text-green-900" title="Editar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </a>
                                        <button wire:click="deleteCurso({{ $curso->idCurso }})" wire:confirm="¿Estás seguro de eliminar este curso?" class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- ---------- Mobile: Cards (visible en sm:hidden) ---------- -->
                <div class="sm:hidden space-y-4 px-4">
                    @foreach($cursos as $curso)
                        <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mr-4">
                                    @if($curso->imagen_portada)
                                        <img src="{{ asset('storage/' . $curso->imagen_portada) }}" alt="{{ $curso->nombre }}" class="h-14 w-14 rounded-lg object-cover border border-gray-200">
                                    @else
                                        <div class="h-14 w-14 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                            <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5"/></svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900 truncate">{{ $curso->nombre }}</div>
                                            <div class="text-xs text-gray-500 truncate">{{ Str::limit($curso->descripcion, 80) }}</div>
                                        </div>
                                        <div class="text-right ml-4">
                                            <div class="text-sm font-medium text-gray-900">${{ number_format($curso->precioFinal, 2) }}</div>
                                            <div class="text-xs text-gray-500">{{ $curso->fecha_inicio ? $curso->fecha_inicio->format('d/m/Y') : '—' }}</div>
                                        </div>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-xs text-gray-600">{{ $curso->cupo_disponible }} / {{ $curso->cupo_total }} cupos</span>
                                            @if($curso->destacado)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Destacado</span>
                                            @endif
                                        </div>

                                        <div class="flex items-center space-x-3">
                                            <a href="{{ route('admin.cursos.show', $curso->idCurso) }}" class="text-blue-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                            </a>
                                            <a href="{{ route('admin.cursos.edit', $curso->idCurso) }}" class="text-green-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11"/></svg>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Paginación -->
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $cursos->links() }}
                </div>
            @else
                <!-- Estado vacío -->
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13"/></svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No hay cursos</h3>
                    <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer curso.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.cursos.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                            Nuevo Curso
                        </a>
                    </div>
                </div>
            @endif
        </div>

        {{--    fin de la tabla--}}

    <!-- Script para toast notifications -->
    <script>
        document.addEventListener('livewire:initialized', () => {
        @this.on('show-toast', (event) => {
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white ${
                event.type === 'success' ? 'bg-green-500' :
                    event.type === 'error' ? 'bg-red-500' :
                        'bg-yellow-500'
            }`;
            toast.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="${event.type === 'success' ? 'M5 13l4 4L19 7' :
                event.type === 'error' ? 'M6 18L18 6M6 6l12 12' :
                    'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z'}"/>
                        </svg>
                        ${event.message}
                    </div>
                `;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.remove();
            }, 3000);
        });
    });
    </script>
</div>
