<?php

namespace App\Livewire\Estudiante;

use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

#[Layout('layouts.app')]
class MisCursos extends Component
{
    use WithPagination;

    public $search = '';
    public $sortBy = 'fecha_inscripcion';
    public $sortDirection = 'desc';

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

        if (!$estudiante) {
            return collect();
        }

        $query = $estudiante->cursos()
            ->withPivot('estado', 'progreso', 'temario_progreso', 'fecha_inscripcion');

        // Aplicar búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
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

        if (!is_array($temarioProgreso)) {
            $temarioProgreso = [];
        }

        $completados = count(array_filter($temarioProgreso, fn ($valor) => $valor === true));

        return round(($completados / $totalItems) * 100, 2);
    }

    public function render()
    {
        return view('livewire.estudiante.mis-cursos');
    }
}
