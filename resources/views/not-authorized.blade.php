
<x-app-layout>
	<x-slot name="header">
		<h2 class="font-semibold text-xl text-gray-800 leading-tight">No autorizado</h2>
	</x-slot>

	<div class="py-12">
		<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
			<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
				<h1 class="text-2xl font-bold mb-4">Acceso denegado</h1>
				<p class="mb-4">No tienes permisos para ver esta página. Si crees que deberías tener acceso, contacta al administrador.</p>
				<a href="{{ url('/') }}" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Volver al inicio</a>
			</div>
		</div>
	</div>
</x-app-layout>

