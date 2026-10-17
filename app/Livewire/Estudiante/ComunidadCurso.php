<?php

namespace App\Livewire\Estudiante;

use App\Models\Curso;
use App\Models\CursoNetworking;
use App\Models\CursoPregunta;
use App\Models\CursoRecurso;
use App\Models\CursoRespuesta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ComunidadCurso extends Component
{
    use WithPagination;

    public Curso $curso;

    public string $tab = 'preguntas';

    // Preguntas y respuestas
    public bool $showModalPregunta = false;

    public string $tituloPregunta = '';

    public string $contenidoPregunta = '';

    public ?int $preguntaActivaId = null;

    public string $nuevaRespuesta = '';

    public string $filtroPreguntas = 'todas';

    // Networking
    public string $nuevoMensaje = '';

    public string $tipoMensaje = 'mensaje';

    // Recursos
    public string $filtroRecursos = 'todos';

    public function mount(Curso $curso): void
    {
        $this->authorize('viewAny', CursoPregunta::class);

        $estudiante = Auth::user()?->estudiante;

        abort_unless(
            $estudiante && $estudiante->cursos()->where('curso_id', $curso->codigo)->exists(),
            403,
            'Debes estar inscrito en este curso para acceder a la comunidad.'
        );

        $this->curso = $curso;
    }

    /**
     * Indica si el estudiante ya finalizó este curso.
     */
    #[Computed]
    public function cursoCompletado(): bool
    {
        return $this->estadoInscripcion() === 'completado';
    }

    /**
     * Estado de la inscripción del estudiante en este curso.
     */
    protected function estadoInscripcion(): ?string
    {
        $estudiante = Auth::user()?->estudiante;

        if (! $estudiante) {
            return null;
        }

        $inscripcion = $this->curso->estudiantes()
            ->where('estudiante_id', $estudiante->codigo)
            ->first();

        return $inscripcion?->pivot?->estado;
    }

    /**
     * Indica si la comunidad está deshabilitada para el estudiante actual.
     * El administrador controla si se mantiene habilitada tras finalizar el curso.
     */
    #[Computed]
    public function comunidadDeshabilitada(): bool
    {
        return ! $this->curso->comunidadDisponiblePara($this->estadoInscripcion());
    }

    /**
     * Aborta la acción si la comunidad está deshabilitada para el estudiante.
     */
    protected function asegurarCursoActivo(): void
    {
        abort_if(
            $this->comunidadDeshabilitada,
            403,
            'La comunidad de este curso está deshabilitada.'
        );
    }

    public function updatedTab(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroPreguntas(): void
    {
        $this->resetPage();
    }

    public function updatedFiltroRecursos(): void
    {
        $this->resetPage();
    }

    /**
     * Preguntas del curso con sus respuestas y autores.
     */
    #[Computed]
    public function preguntas()
    {
        $query = CursoPregunta::query()
            ->where('curso_id', $this->curso->codigo)
            ->with(['estudiante.user', 'respuestas.estudiante.user']);

        if ($this->filtroPreguntas === 'resueltas') {
            $query->where('resuelta', true);
        } elseif ($this->filtroPreguntas === 'pendientes') {
            $query->where('resuelta', false);
        }

        return $query->latest()->paginate(10, ['*'], 'paginaPreguntas');
    }

    /**
     * Publicaciones de networking del curso.
     */
    #[Computed]
    public function publicaciones()
    {
        return CursoNetworking::query()
            ->where('curso_id', $this->curso->codigo)
            ->with('estudiante.user')
            ->latest()
            ->paginate(10, ['*'], 'paginaNetworking');
    }

    /**
     * Recursos descargables publicados del curso.
     */
    #[Computed]
    public function recursos()
    {
        $query = CursoRecurso::query()
            ->where('curso_id', $this->curso->codigo)
            ->publicados();

        if ($this->filtroRecursos !== 'todos') {
            $query->where('tipo', $this->filtroRecursos);
        }

        return $query->latest()->paginate(9, ['*'], 'paginaRecursos');
    }

    /**
     * Compañeros inscritos en el curso (para networking).
     */
    #[Computed]
    public function companeros()
    {
        return $this->curso->estudiantes()
            ->with('user')
            ->wherePivot('estado', '!=', 'rechazado')
            ->get();
    }

    /**
     * Estadísticas rápidas de la comunidad.
     */
    #[Computed]
    public function estadisticas(): array
    {
        return [
            'preguntas' => CursoPregunta::where('curso_id', $this->curso->codigo)->count(),
            'resueltas' => CursoPregunta::where('curso_id', $this->curso->codigo)->where('resuelta', true)->count(),
            'recursos' => CursoRecurso::where('curso_id', $this->curso->codigo)->publicados()->count(),
            'companeros' => $this->companeros->count(),
        ];
    }

    public function abrirModalPregunta(): void
    {
        $this->asegurarCursoActivo();
        $this->authorize('create', CursoPregunta::class);

        $this->reset(['tituloPregunta', 'contenidoPregunta']);
        $this->resetValidation();
        $this->showModalPregunta = true;
    }

    public function cerrarModalPregunta(): void
    {
        $this->showModalPregunta = false;
        $this->reset(['tituloPregunta', 'contenidoPregunta']);
        $this->resetValidation();
    }

    public function crearPregunta(): void
    {
        $this->asegurarCursoActivo();
        $this->authorize('create', CursoPregunta::class);

        $this->validate([
            'tituloPregunta' => ['required', 'string', 'min:5', 'max:150'],
            'contenidoPregunta' => ['required', 'string', 'min:10', 'max:2000'],
        ], [], [
            'tituloPregunta' => 'título',
            'contenidoPregunta' => 'contenido',
        ]);

        $estudiante = Auth::user()->estudiante;

        CursoPregunta::create([
            'curso_id' => $this->curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'titulo' => $this->tituloPregunta,
            'contenido' => $this->contenidoPregunta,
        ]);

        $this->cerrarModalPregunta();
        unset($this->preguntas, $this->estadisticas);

        $this->dispatch('show-toast', type: 'success', message: 'Tu pregunta fue publicada.');
    }

    public function seleccionarPregunta(int $preguntaId): void
    {
        $pregunta = CursoPregunta::findOrFail($preguntaId);
        $this->authorize('view', $pregunta);

        $this->preguntaActivaId = $this->preguntaActivaId === $preguntaId ? null : $preguntaId;
        $this->nuevaRespuesta = '';
        $this->resetValidation();
    }

    public function responderPregunta(): void
    {
        $this->asegurarCursoActivo();

        $this->validate([
            'nuevaRespuesta' => ['required', 'string', 'min:3', 'max:2000'],
        ], [], ['nuevaRespuesta' => 'respuesta']);

        $pregunta = CursoPregunta::findOrFail($this->preguntaActivaId);
        $this->authorize('view', $pregunta);
        $this->authorize('create', CursoRespuesta::class);

        CursoRespuesta::create([
            'pregunta_id' => $pregunta->id,
            'estudiante_id' => Auth::user()->estudiante->codigo,
            'contenido' => $this->nuevaRespuesta,
        ]);

        $this->nuevaRespuesta = '';
        unset($this->preguntas);

        $this->dispatch('show-toast', type: 'success', message: 'Respuesta publicada.');
    }

    public function marcarResuelta(int $preguntaId): void
    {
        $this->asegurarCursoActivo();

        $pregunta = CursoPregunta::findOrFail($preguntaId);
        $this->authorize('update', $pregunta);

        $pregunta->update(['resuelta' => ! $pregunta->resuelta]);

        unset($this->preguntas, $this->estadisticas);

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: $pregunta->resuelta ? 'Pregunta marcada como resuelta.' : 'Pregunta reabierta.'
        );
    }

    public function eliminarPregunta(int $preguntaId): void
    {
        $this->asegurarCursoActivo();

        $pregunta = CursoPregunta::findOrFail($preguntaId);
        $this->authorize('delete', $pregunta);

        $pregunta->delete();

        if ($this->preguntaActivaId === $preguntaId) {
            $this->preguntaActivaId = null;
        }

        unset($this->preguntas, $this->estadisticas);

        $this->dispatch('show-toast', type: 'success', message: 'Pregunta eliminada.');
    }

    public function eliminarRespuesta(int $respuestaId): void
    {
        $this->asegurarCursoActivo();

        $respuesta = CursoRespuesta::findOrFail($respuestaId);
        $this->authorize('delete', $respuesta);

        $respuesta->delete();

        unset($this->preguntas);

        $this->dispatch('show-toast', type: 'success', message: 'Respuesta eliminada.');
    }

    public function publicarMensaje(): void
    {
        $this->asegurarCursoActivo();
        $this->authorize('create', CursoNetworking::class);

        $this->validate([
            'nuevoMensaje' => ['required', 'string', 'min:3', 'max:1000'],
            'tipoMensaje' => ['required', 'in:mensaje,presentacion,colaboracion'],
        ], [], ['nuevoMensaje' => 'mensaje']);

        CursoNetworking::create([
            'curso_id' => $this->curso->codigo,
            'estudiante_id' => Auth::user()->estudiante->codigo,
            'mensaje' => $this->nuevoMensaje,
            'tipo' => $this->tipoMensaje,
        ]);

        $this->reset(['nuevoMensaje']);
        $this->tipoMensaje = 'mensaje';
        unset($this->publicaciones);

        $this->dispatch('show-toast', type: 'success', message: 'Publicación compartida con tus compañeros.');
    }

    public function eliminarPublicacion(int $publicacionId): void
    {
        $this->asegurarCursoActivo();

        $publicacion = CursoNetworking::findOrFail($publicacionId);
        $this->authorize('delete', $publicacion);

        $publicacion->delete();

        unset($this->publicaciones);

        $this->dispatch('show-toast', type: 'success', message: 'Publicación eliminada.');
    }

    /**
     * Descarga un recurso del curso verificando la inscripción del estudiante.
     */
    public function descargarRecurso(int $recursoId)
    {
        $this->asegurarCursoActivo();

        $recurso = CursoRecurso::findOrFail($recursoId);
        $this->authorize('view', $recurso);

        if (! $recurso->archivo_path || ! Storage::disk('public')->exists($recurso->archivo_path)) {
            $this->dispatch('show-toast', type: 'error', message: 'El archivo no está disponible.');

            return null;
        }

        return Storage::disk('public')->download($recurso->archivo_path, $recurso->titulo);
    }

    public function render()
    {
        return view('livewire.estudiante.comunidad-curso');
    }
}
