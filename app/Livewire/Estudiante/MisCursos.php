<?php

namespace App\Livewire\Estudiante;

use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MisCursos extends Component
{
    use WithPagination;

    public $search = '';

    public $sortBy = 'fecha_inscripcion';

    public $sortDirection = 'desc';

    public $evaluaciones = [];

    public bool $showModalOpinion = false;

    public string $cursoSeleccionadoCodigo = '';

    public string $cursoSeleccionadoNombre = '';

    public ?int $ratingEstudiante = null;

    public string $opinionEstudiante = '';

    public string $tab = 'activos';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function updatedTab()
    {
        $this->resetPage();
    }

    #[Computed]
    public function misCursos()
    {
        $user = Auth::user();
        $estudiante = $user?->estudiante;

        if (! $estudiante) {
            return collect();
        }

        $query = $estudiante->cursos()
            ->with('instructor')
            ->withPivot('estado', 'progreso', 'temario_progreso', 'fecha_inscripcion', 'calificacion', 'rating_estudiante', 'opinion_estudiante');

        // Filtrar por pestaña
        if ($this->tab === 'completados') {
            $query->wherePivot('estado', 'completado');
        } else {
            $query->wherePivot('estado', '!=', 'completado');
        }

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%');
            });
        }

        // Aplicar orden
        if ($this->sortBy === 'fecha_inscripcion') {
            $query->orderBy('curso_estudiante.fecha_inscripcion', $this->sortDirection);
        } else {
            $query->orderBy($this->sortBy, $this->sortDirection);
        }

        $cursos = $query->paginate(9);

        // Calcular progreso real basado en el temario para cada curso
        $cursos->each(function ($curso) {
            $progreso = $this->calcularProgresoCurso($curso);
            $curso->pivot->progreso = $progreso;
        });

        return $cursos;
    }

    protected function calcularProgresoCurso($curso): float
    {
        $temarioItems = $curso->temario_items;
        $totalItems = count($temarioItems);

        if ($totalItems === 0) {
            return 0.0;
        }

        $temarioProgreso = $curso->pivot->temario_progreso ?? [];

        if (is_string($temarioProgreso)) {
            $temarioProgreso = json_decode($temarioProgreso, true) ?? [];
        }

        if (! is_array($temarioProgreso)) {
            $temarioProgreso = [];
        }

        $completados = count(array_filter($temarioProgreso, fn ($valor) => $valor === true));

        return round(($completados / $totalItems) * 100, 2);
    }

    public function abrirModalOpinion(string $codigo, string $nombre): void
    {
        $estudiante = Auth::user()?->estudiante;

        if (! $estudiante) {
            return;
        }

        $curso = $estudiante->cursos()
            ->where('curso_id', $codigo)
            ->first();

        $this->cursoSeleccionadoCodigo = $codigo;
        $this->cursoSeleccionadoNombre = $nombre;
        $this->ratingEstudiante = $curso?->pivot?->rating_estudiante;
        $this->opinionEstudiante = $curso?->pivot?->opinion_estudiante ?? '';
        $this->showModalOpinion = true;
    }

    public function cerrarModalOpinion(): void
    {
        $this->showModalOpinion = false;
        $this->cursoSeleccionadoCodigo = '';
        $this->cursoSeleccionadoNombre = '';
        $this->ratingEstudiante = null;
        $this->opinionEstudiante = '';
    }

    public function guardarOpinion(): void
    {
        if (! $this->ratingEstudiante) {
            $this->dispatch('show-toast', type: 'error', message: 'Debes seleccionar una puntuación.');

            return;
        }

        $estudiante = Auth::user()?->estudiante;

        if (! $estudiante) {
            return;
        }

        $estudiante->cursos()->updateExistingPivot($this->cursoSeleccionadoCodigo, [
            'rating_estudiante' => $this->ratingEstudiante,
            'opinion_estudiante' => $this->opinionEstudiante,
        ]);

        $this->cerrarModalOpinion();

        $this->dispatch('show-toast', type: 'success', message: '¡Gracias por tu opinión!');
    }

    public function registrarEvaluacion($cursoCodigo)
    {
        $calificacion = $this->evaluaciones[$cursoCodigo]['calificacion'] ?? null;
        $comentario = $this->evaluaciones[$cursoCodigo]['comentario'] ?? null;

        if (! $calificacion || ! $comentario) {
            $this->dispatch('show-toast', type: 'error', message: 'Debes ingresar una puntuación y un comentario.');

            return;
        }

        $estudiante = Auth::user()->estudiante;

        $estudiante->cursos()->updateExistingPivot($cursoCodigo, [
            'calificacion' => $calificacion,
            'comentario_calificacion' => $comentario,
        ]);

        $this->dispatch('show-toast', type: 'success', message: '¡Gracias por tu calificación!');
    }

    public function marcarComoCompletado($cursoCodigo)
    {
        $estudiante = Auth::user()->estudiante;

        $estudiante->cursos()->updateExistingPivot($cursoCodigo, [
            'estado' => 'completado',
            'fecha_completado' => now(),
            'progreso' => 100,
        ]);

        $this->dispatch('show-toast', type: 'success', message: '¡Curso marcado como finalizado!');
    }

    public function render()
    {
        return view('livewire.estudiante.mis-cursos');
    }
}
