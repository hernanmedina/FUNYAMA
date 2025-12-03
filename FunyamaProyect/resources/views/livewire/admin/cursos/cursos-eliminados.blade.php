<div>
    {{-- Nothing in the world is as soft and yielding as water. --}}
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="text-2xl font-semibold mb-4">Cursos eliminados</h2>

    @if ($cursosEliminados->isEmpty())
        <p class="text-gray-600">No hay cursos eliminados.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white border">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-left">Nombre</th>
                    <th class="px-4 py-2 text-left">Slug</th>
                    <th class="px-4 py-2 text-left">Eliminado el</th>
                    <th class="px-4 py-2 text-left">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($cursosEliminados as $curso)
                    <tr class="border-t">
                        <td class="px-4 py-2">{{ $curso->nombre }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ $curso->slug }}</td>
                        <td class="px-4 py-2 text-sm text-gray-600">{{ optional($curso->deleted_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-2">
                            <button wire:click="restaurarCurso('{{ $curso->codigo }}')" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded mr-2">
                                Restaurar
                            </button>

                            <button onclick="if(!confirm('¿Eliminar permanentemente este curso? Esta acción no se puede deshacer.')){ event.stopImmediatePropagation(); }" wire:click="eliminarPermanentemente('{{ $curso->codigo }}')" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                                Eliminar permanentemente
                            </button>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
