<?php
// app/Http/Livewire/Admin/EstudiantesEliminados.php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use Livewire\Component;
use Livewire\WithPagination;

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
        return Estudiante::onlyTrashed()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('telefono', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage);
    }

    public function restaurar($idEstudiante)
    {
        $estudiante = Estudiante::onlyTrashed()->findOrFail($idEstudiante);
        $estudiante->restore();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Estudiante restaurado correctamente.'
        ]);
    }

    public function restaurarSeleccionados()
    {
        if (count($this->selected) > 0) {
            Estudiante::onlyTrashed()
                ->whereIn('idEstudiante', $this->selected)
                ->restore();

            $this->selected = [];
            $this->selectAll = false;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Estudiantes seleccionados restaurados correctamente.'
            ]);
        }
    }

    public function eliminarPermanentemente($idEstudiante)
    {
        $estudiante = Estudiante::onlyTrashed()->findOrFail($idEstudiante);

        // Si tienes relaciones, eliminarlas aquí primero
        // $estudiante->cursos()->detach();

        $estudiante->forceDelete();

        $this->dispatch('show-toast', [
            'type' => 'success',
            'message' => 'Estudiante eliminado permanentemente.'
        ]);
    }

    public function eliminarSeleccionadosPermanentemente()
    {
        if (count($this->selected) > 0) {
            $estudiantes = Estudiante::onlyTrashed()
                ->whereIn('idEstudiante', $this->selected)
                ->get();

            foreach ($estudiantes as $estudiante) {
                // Eliminar relaciones si es necesario
                // $estudiante->cursos()->detach();
                $estudiante->forceDelete();
            }

            $this->selected = [];
            $this->selectAll = false;

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Estudiantes seleccionados eliminados permanentemente.'
            ]);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->estudiantesEliminados->pluck('idEstudiante')->toArray();
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
