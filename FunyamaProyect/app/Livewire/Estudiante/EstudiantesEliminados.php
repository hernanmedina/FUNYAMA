<?php
// app/Http/Livewire/Admin/EstudiantesEliminados.php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class EstudiantesEliminados extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selected = [];
    public $selectAll = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10]
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function getEstudiantesEliminadosProperty()
    {
        $query = Estudiante::onlyTrashed()
            ->select('estudiantes.*')
            ->join('users', 'estudiantes.user_id', '=', 'users.id')
            ->with('user');

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('users.name', 'like', $s)
                    ->orWhere('users.apellido', 'like', $s)
                    ->orWhere('users.email', 'like', $s)
                    ->orWhere('users.telefono', 'like', $s)
                    ->orWhere('estudiantes.codigo', 'like', $s);
            });
        }

        return $query->orderBy('estudiantes.deleted_at', 'desc')
            ->paginate($this->perPage);
    }

    public function restaurar($codigo)
    {
        $estudiante = Estudiante::onlyTrashed()->where('codigo', $codigo)->firstOrFail();
        $estudiante->restore();
        $this->resetPage();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Estudiante restaurado correctamente.'
        ]);
    }

    public function restaurarSeleccionados()
    {
        if (count($this->selected) > 0) {
            Estudiante::onlyTrashed()
                ->whereIn('codigo', $this->selected)
                ->restore();

            $this->selected = [];
            $this->selectAll = false;
            $this->resetPage();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Estudiantes seleccionados restaurados correctamente.'
            ]);
        }
    }

    public function eliminarPermanentemente($codigo)
    {
        $estudiante = Estudiante::onlyTrashed()->where('codigo', $codigo)->firstOrFail();

        // Si tienes relaciones, eliminarlas aquí primero
        // $estudiante->cursos()->detach();

        $estudiante->forceDelete();
        $this->resetPage();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Estudiante eliminado permanentemente.'
        ]);
    }

    public function eliminarSeleccionadosPermanentemente()
    {
        if (count($this->selected) > 0) {
            $estudiantes = Estudiante::onlyTrashed()
                ->whereIn('codigo', $this->selected)
                ->get();

            foreach ($estudiantes as $estudiante) {
                // Eliminar relaciones si es necesario
                // $estudiante->cursos()->detach();
                $estudiante->forceDelete();
            }

            $this->selected = [];
            $this->selectAll = false;
            $this->resetPage();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Estudiantes seleccionados eliminados permanentemente.'
            ]);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->estudiantesEliminados->pluck('codigo')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function render()
    {
        return view('livewire.estudiante.estudiantes-eliminados', [
            'estudiantes' => $this->estudiantesEliminados
        ])->layout('layouts.app');
    }
}
