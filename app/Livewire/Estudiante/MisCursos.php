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

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedSortBy()
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
            ->withPivot('estado', 'progreso', 'temario_progreso', 'fecha_inscripcion');

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
