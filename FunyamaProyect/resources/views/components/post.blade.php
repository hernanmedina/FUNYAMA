<div class="p-6 lg:p-8 bg-white border-b border-gray-200">
    {{-- <x-application-logo class="block h-12 w-auto" /> --}}

    <h1 class="mt-8 text-2xl font-medium text-gray-900">
        Panel de Administración
    </h1>

    <p class="mt-6 text-gray-500 leading-relaxed">
        Bienvenido {{ Auth::user()->name }} al panel de administración.
    </p>
    <p>
         Aquí puedes gestionar el contenido de la plataforma.
    </p>
</div>

<div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8 p-6 lg:p-8">
    {{-- Validación de rol --}}
    @if(Auth::check() && Auth::user()->role === 'admin')
        <div class="col-span-2">
            <div class="flex items-center mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                      stroke-width="1.5" class="size-6 stroke-gray-400">
                    <path stroke-linecap="round" stroke-linejoin="round"
                           d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                <h2 class="ms-3 text-xl font-semibold text-gray-900">
                    Opciones de Administración
                </h2>
            </div>

            {{-- Formulario de creación de post
            <form action=" " method="POST" class="bg-white shadow-md rounded-lg p-6">
                @csrf

                <div class="mb-4">
                    <label for="title" class="block text-gray-700">Título</label>
                    <input type="text" name="title" id="title"
                            class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                            required>
                </div>

                <div class="mb-4">
                    <label for="content" class="block text-gray-700">Contenido</label>
                    <textarea name="content" id="content" rows="5"
                              class="w-full border rounded px-3 py-2 focus:outline-none focus:ring"
                              required></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Publicar
                    </button>
                </div>
            </form> --}}
        </div>
    @else
        <div class="bg-red-100 text-red-700 p-4 rounded col-span-2">
            No tienes permisos para acceder a este panel.
        </div>
    @endif
</div>
