<?php
// app/Http/Livewire/Estudiantes/Estudiantes.php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;

class Estudiantes extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selected = [];
    public $selectAll = false;
    public $sortField = 'nombre';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'sortField' => ['except' => 'nombre'],
        'sortDirection' => ['except' => 'asc']
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
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

    public function getEstudiantesProperty()
    {
        return Estudiante::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('email', 'like', '%' . $this->search . '%')
                        ->orWhere('telefono', 'like', '%' . $this->search . '%')
                        ->orWhere('documento_identidad', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function toggleEstado($idEstudiante)
    {
        $estudiante = Estudiante::findOrFail($idEstudiante);
        $estudiante->estado = $estudiante->estado === 'activo' ? 'inactivo' : 'activo';
        $estudiante->save();

        session()->flash('message', 'Estado del estudiante actualizado correctamente.');
    }

    public function deleteEstudiante($idEstudiante)
    {
        $estudiante = Estudiante::findOrFail($idEstudiante);
        $estudiante->delete(); // Soft delete

        session()->flash('message', 'Estudiante eliminado correctamente.');
    }

    public function bulkDelete()
    {
        if (count($this->selected) > 0) {
            Estudiante::whereIn('idEstudiante', $this->selected)->delete();

            $this->selected = [];
            $this->selectAll = false;

            session()->flash('message', 'Estudiantes seleccionados eliminados correctamente.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->estudiantes->pluck('idEstudiante')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function render()
    {
        return view('livewire.estudiante.estudiantes', [
            'estudiantes' => $this->estudiantes
        ])->layout('layouts.app');
    }
}
