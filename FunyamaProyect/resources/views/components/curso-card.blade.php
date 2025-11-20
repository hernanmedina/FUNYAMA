<div class="bg-white rounded-xl shadow-lg overflow-hidden border border-gray-200">

    <!-- Header Azul -->
    <div class="bg-blue-600 px-6 py-4">
        <h2 class="text-xl md:text-2xl font-bold text-white">
            {{ $nombre }}
        </h2>
    </div>

    <!-- Contenido -->
    <div class="px-6 py-4 space-y-4">

        <div>
            <h3 class="font-semibold text-blue-700">Cronograma:</h3>
            <p class="text-gray-700">{{ $cronograma }}</p>
        </div>

        <div>
            <h3 class="font-semibold text-blue-700">Requisitos:</h3>
            <p class="text-gray-700">{{ $requisitos }}</p>
        </div>

        <hr class="my-3">

        <div class="flex items-center justify-between">
            <span class="font-semibold text-gray-800">Cupo disponible:</span>
            <span class="text-blue-700 font-bold text-lg">
                {{ $cupo }}
            </span>
        </div>
    </div>
</div>
