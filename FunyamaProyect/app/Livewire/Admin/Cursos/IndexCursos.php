<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use App\Models\Curso;
use Livewire\WithPagination;

class IndexCursos extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';
    public $selected = []; // Inicializar como array vacío
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        // Asegurarnos de que selected esté inicializado
        $this->selected = [];
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortField = $field;
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->cursos->pluck('codigo')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function deleteCurso($cursoCodigo)
    {
        $curso = Curso::where('codigo', $cursoCodigo)->firstOrFail();

        // Verificar si hay estudiantes inscritos antes de eliminar
        if ($curso->estudiantes()->count() > 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No se puede eliminar el curso porque tiene estudiantes inscritos.'
            );
            return;
        }

        $curso->delete();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Curso eliminado correctamente.'
        );
    }

    public function bulkDelete()
    {
        // Verificar que selected no sea null
        if (!$this->selected || count($this->selected) === 0) {
            $this->dispatch('show-toast',
                type: 'warning',
                message: 'Selecciona al menos un curso para eliminar.'
            );
            return;
        }

        // Verificar que ningún curso seleccionado tenga estudiantes
        $cursosConEstudiantes = Curso::whereIn('codigo', $this->selected)
            ->whereHas('estudiantes')
            ->count();

        if ($cursosConEstudiantes > 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Algunos cursos seleccionados tienen estudiantes inscritos y no pueden ser eliminados.'
            );
            return;
        }

        Curso::whereIn('codigo', $this->selected)->delete();

        $this->selected = [];
        $this->dispatch('show-toast',
            type: 'success',
            message: 'Cursos eliminados correctamente.'
        );
    }

    public function togglePublicacion($cursoId)
    {
        $curso = Curso::findOrFail($cursoId);
        $curso->update(['publicado' => !$curso->publicado]);

        $action = $curso->publicado ? 'publicado' : 'ocultado';
        $this->dispatch('show-toast',
            type: 'success',
            message: "Curso {$action} correctamente."
        );
    }

    public function render()
    {
        $cursos = Curso::query()
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.cursos.index-cursos', [
            'cursos' => $cursos,
        ])->layout('layouts.app');
    }
}
