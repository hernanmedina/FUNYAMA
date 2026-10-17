<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        {{-- Encabezado --}}
        <div class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-lg shadow-lg p-8 mb-8 text-white">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-blue-200 text-sm font-semibold uppercase tracking-wide">Comunidad privada del curso</p>
                    <h1 class="text-3xl font-bold mt-1 text-white">{{ $curso->nombre }}</h1>
                    <p class="text-blue-100 mt-2">
                        Sesiones de preguntas y respuestas, networking con tus compañeros y material descargable.
                    </p>
                </div>
                <a href="{{ route('estudiante.mis-cursos') }}"
                   class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 text-white rounded-lg text-sm font-medium transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Mis cursos
                </a>
            </div>

            {{-- Estadísticas --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-6">
                <div class="bg-white/10 rounded-lg p-4">
                    <p class="text-2xl font-bold">{{ $this->estadisticas['preguntas'] }}</p>
                    <p class="text-blue-100 text-sm">Preguntas</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <p class="text-2xl font-bold">{{ $this->estadisticas['resueltas'] }}</p>
                    <p class="text-blue-100 text-sm">Resueltas</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <p class="text-2xl font-bold">{{ $this->estadisticas['recursos'] }}</p>
                    <p class="text-blue-100 text-sm">Recursos</p>
                </div>
                <div class="bg-white/10 rounded-lg p-4">
                    <p class="text-2xl font-bold">{{ $this->estadisticas['companeros'] }}</p>
                    <p class="text-blue-100 text-sm">Compañeros</p>
                </div>
            </div>
        </div>

        {{-- Pestañas --}}
        <div class="bg-white rounded-lg shadow-md mb-6">
            <nav class="flex flex-wrap border-b border-slate-200">
                <button wire:click="$set('tab', 'preguntas')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $tab === 'preguntas' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Preguntas y respuestas
                </button>
                <button wire:click="$set('tab', 'networking')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $tab === 'networking' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Networking
                </button>
                <button wire:click="$set('tab', 'recursos')"
                        class="px-6 py-4 text-sm font-medium border-b-2 transition {{ $tab === 'recursos' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700' }}">
                    Material descargable
                </button>
            </nav>
        </div>

        {{-- Aviso: comunidad deshabilitada --}}
        @if ($this->comunidadDeshabilitada)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-6 flex items-start gap-4">
                <svg class="w-6 h-6 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19h13.86a2 2 0 001.74-3L13.74 4a2 2 0 00-3.48 0L3.33 16a2 2 0 001.74 3z"/>
                </svg>
                <div>
                    <h3 class="text-base font-semibold text-amber-800">Comunidad deshabilitada</h3>
                    <p class="text-sm text-amber-700 mt-1">
                        La comunidad de este curso está deshabilitada. Puedes seguir consultando las preguntas y
                        publicaciones, pero no crear contenido nuevo ni descargar el material del curso.
                    </p>
                </div>
            </div>
        @endif

        {{-- Preguntas y respuestas --}}
        @if ($tab === 'preguntas')
            <div class="space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex flex-wrap gap-2">
                        @foreach (['todas' => 'Todas', 'pendientes' => 'Pendientes', 'resueltas' => 'Resueltas'] as $valor => $etiqueta)
                            <button wire:click="$set('filtroPreguntas', '{{ $valor }}')"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filtroPreguntas === $valor ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
                                {{ $etiqueta }}
                            </button>
                        @endforeach
                    </div>
                    @unless ($this->comunidadDeshabilitada)
                        <button wire:click="abrirModalPregunta"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Nueva pregunta
                        </button>
                    @endunless
                </div>

                @forelse ($this->preguntas as $pregunta)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden" wire:key="pregunta-{{ $pregunta->id }}">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $pregunta->resuelta ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $pregunta->resuelta ? 'Resuelta' : 'Pendiente' }}
                                        </span>
                                        <span class="text-xs text-slate-500">
                                            {{ $pregunta->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                    <h3 class="text-lg font-semibold text-slate-900">{{ $pregunta->titulo }}</h3>
                                    <p class="text-slate-600 mt-2 whitespace-pre-line">{{ $pregunta->contenido }}</p>
                                    <p class="text-sm text-slate-500 mt-3">
                                        Por {{ $pregunta->estudiante?->user?->nombre_completo ?? 'Estudiante' }}
                                    </p>
                                </div>
                                <div class="flex flex-col gap-2">
                                    <button wire:click="seleccionarPregunta({{ $pregunta->id }})"
                                            class="text-sm text-blue-600 hover:text-blue-800 font-medium whitespace-nowrap">
                                        {{ $preguntaActivaId === $pregunta->id ? 'Ocultar' : 'Responder' }}
                                        ({{ $pregunta->respuestas->count() }})
                                    </button>
                                    @if ($pregunta->estudiante_id === auth()->user()?->estudiante?->codigo && ! $this->comunidadDeshabilitada)
                                        <button wire:click="marcarResuelta({{ $pregunta->id }})"
                                                class="text-sm text-slate-500 hover:text-slate-700">
                                            {{ $pregunta->resuelta ? 'Reabrir' : 'Marcar resuelta' }}
                                        </button>
                                        <button wire:click="eliminarPregunta({{ $pregunta->id }})"
                                                wire:confirm="¿Eliminar esta pregunta?"
                                                class="text-sm text-red-600 hover:text-red-800">
                                            Eliminar
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if ($preguntaActivaId === $pregunta->id)
                            <div class="bg-slate-50 border-t border-slate-200 p-6 space-y-4">
                                @forelse ($pregunta->respuestas as $respuesta)
                                    <div class="bg-white rounded-lg p-4 border border-slate-200" wire:key="respuesta-{{ $respuesta->id }}">
                                        <p class="text-slate-700 whitespace-pre-line">{{ $respuesta->contenido }}</p>
                                        <div class="flex items-center justify-between mt-3">
                                            <p class="text-xs text-slate-500">
                                                {{ $respuesta->estudiante?->user?->nombre_completo ?? 'Estudiante' }}
                                                · {{ $respuesta->created_at->diffForHumans() }}
                                            </p>
                                            @if ($respuesta->estudiante_id === auth()->user()?->estudiante?->codigo && ! $this->comunidadDeshabilitada)
                                                <button wire:click="eliminarRespuesta({{ $respuesta->id }})"
                                                        wire:confirm="¿Eliminar esta respuesta?"
                                                        class="text-xs text-red-600 hover:text-red-800">
                                                    Eliminar
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-sm text-slate-500">Aún no hay respuestas. ¡Sé el primero en ayudar!</p>
                                @endforelse

                                @unless ($this->comunidadDeshabilitada)
                                    <div class="pt-2">
                                        <textarea wire:model="nuevaRespuesta" rows="3"
                                                  class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                                  placeholder="Escribe tu respuesta..."></textarea>
                                        @error('nuevaRespuesta')
                                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                                        @enderror
                                        <button wire:click="responderPregunta"
                                                class="mt-3 inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                            Publicar respuesta
                                        </button>
                                    </div>
                                @endunless
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white rounded-lg shadow-md p-12 text-center">
                        <p class="text-slate-500">Todavía no hay preguntas en este curso.</p>
                    </div>
                @endforelse

                <div>{{ $this->preguntas->links() }}</div>
            </div>
        @endif

        {{-- Networking --}}
        @if ($tab === 'networking')
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 space-y-6">
                    @unless ($this->comunidadDeshabilitada)
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <h2 class="text-lg font-semibold text-slate-900 mb-4">Comparte con la comunidad</h2>
                            <textarea wire:model="nuevoMensaje" rows="3"
                                      class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                      placeholder="Preséntate, propón una colaboración o comparte una idea..."></textarea>
                            @error('nuevoMensaje')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mt-4">
                                <select wire:model="tipoMensaje"
                                        class="rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                    <option value="mensaje">Mensaje</option>
                                    <option value="presentacion">Presentación</option>
                                    <option value="colaboracion">Colaboración</option>
                                </select>
                                <button wire:click="publicarMensaje"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                    Publicar
                                </button>
                            </div>
                        </div>
                    @endunless

                    @forelse ($this->publicaciones as $publicacion)
                        <div class="bg-white rounded-lg shadow-md p-6" wire:key="publicacion-{{ $publicacion->id }}">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $publicacion->tipo === 'colaboracion' ? 'bg-purple-100 text-purple-800' : ($publicacion->tipo === 'presentacion' ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-700') }}">
                                            {{ $publicacion->tipo_etiqueta }}
                                        </span>
                                        <span class="text-xs text-slate-500">{{ $publicacion->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-slate-700 whitespace-pre-line">{{ $publicacion->mensaje }}</p>
                                    <p class="text-sm text-slate-500 mt-3">
                                        {{ $publicacion->estudiante?->user?->nombre_completo ?? 'Estudiante' }}
                                    </p>
                                </div>
                                @if ($publicacion->estudiante_id === auth()->user()?->estudiante?->codigo && ! $this->comunidadDeshabilitada)
                                    <button wire:click="eliminarPublicacion({{ $publicacion->id }})"
                                            wire:confirm="¿Eliminar esta publicación?"
                                            class="text-sm text-red-600 hover:text-red-800">
                                        Eliminar
                                    </button>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="bg-white rounded-lg shadow-md p-12 text-center">
                            <p class="text-slate-500">Sé el primero en presentarte ante tus compañeros.</p>
                        </div>
                    @endforelse

                    <div>{{ $this->publicaciones->links() }}</div>
                </div>

                <div class="bg-white rounded-lg shadow-md p-6 h-fit">
                    <h2 class="text-lg font-semibold text-slate-900 mb-4">Compañeros del curso</h2>
                    <ul class="space-y-3">
                        @forelse ($this->companeros as $companero)
                            <li class="flex items-center gap-3" wire:key="companero-{{ $companero->codigo }}">
                                <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-sm font-semibold">
                                    {{ strtoupper(substr($companero->user?->name ?? 'E', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-slate-800 truncate">
                                        {{ $companero->user?->nombre_completo ?? 'Estudiante' }}
                                    </p>
                                    <p class="text-xs text-slate-500 truncate">{{ $companero->user?->email }}</p>
                                </div>
                            </li>
                        @empty
                            <li class="text-sm text-slate-500">Aún no hay compañeros inscritos.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endif

        {{-- Recursos --}}
        @if ($tab === 'recursos')
            <div class="space-y-6">
                <div class="flex flex-wrap gap-2">
                    @foreach (['todos' => 'Todos', 'plantilla' => 'Plantillas', 'guia' => 'Guías', 'recurso' => 'Recursos'] as $valor => $etiqueta)
                        <button wire:click="$set('filtroRecursos', '{{ $valor }}')"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $filtroRecursos === $valor ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-100' }}">
                            {{ $etiqueta }}
                        </button>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($this->recursos as $recurso)
                        <div class="bg-white rounded-lg shadow-md p-6 flex flex-col" wire:key="recurso-{{ $recurso->id }}">
                            <div class="flex items-center justify-between mb-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $recurso->tipo === 'plantilla' ? 'bg-purple-100 text-purple-800' : ($recurso->tipo === 'guia' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $recurso->tipo_etiqueta }}
                                </span>
                                <span class="text-xs text-slate-500">v{{ $recurso->version }}</span>
                            </div>
                            <h3 class="text-base font-semibold text-slate-900">{{ $recurso->titulo }}</h3>
                            @if ($recurso->descripcion)
                                <p class="text-sm text-slate-600 mt-2 flex-1">{{ $recurso->descripcion }}</p>
                            @endif
                            <div class="mt-4 flex flex-wrap gap-2">
                                @if ($this->comunidadDeshabilitada)
                                    <span class="inline-flex items-center px-3 py-2 bg-slate-100 text-slate-500 rounded-lg text-sm font-medium">
                                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                        Descarga no disponible
                                    </span>
                                @else
                                    @if ($recurso->archivo_path)
                                        <button wire:click="descargarRecurso({{ $recurso->id }})"
                                                class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1M12 12V4m0 8l-3-3m3 3l3-3"/>
                                            </svg>
                                            Descargar
                                        </button>
                                    @endif
                                    @if ($recurso->enlace)
                                        <a href="{{ $recurso->enlace }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-medium transition">
                                            Abrir enlace
                                        </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="md:col-span-2 lg:col-span-3 bg-white rounded-lg shadow-md p-12 text-center">
                            <p class="text-slate-500">Todavía no hay material descargable para este curso.</p>
                        </div>
                    @endforelse
                </div>

                <div>{{ $this->recursos->links() }}</div>
            </div>
        @endif
    </div>

    {{-- Modal nueva pregunta --}}
    @if ($showModalPregunta)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="text-lg font-semibold text-slate-900">Nueva pregunta</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Título</label>
                        <input type="text" wire:model="tituloPregunta"
                               class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                               placeholder="Resume tu duda en una frase">
                        @error('tituloPregunta')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Contenido</label>
                        <textarea wire:model="contenidoPregunta" rows="5"
                                  class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500"
                                  placeholder="Describe tu pregunta con el mayor detalle posible"></textarea>
                        @error('contenidoPregunta')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="px-6 py-4 bg-slate-50 flex justify-end gap-3 rounded-b-lg">
                    <button wire:click="cerrarModalPregunta"
                            class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">
                        Cancelar
                    </button>
                    <button wire:click="crearPregunta"
                            class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                        Publicar pregunta
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
