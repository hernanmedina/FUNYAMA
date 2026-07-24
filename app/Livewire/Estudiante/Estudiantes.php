<?php
// app/Http/Livewire/Estudiantes/Estudiantes.php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Estudiante;
use Illuminate\Support\Str;

class Estudiantes extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selected = [];
    public $selectAll = false;
    public $sortField = 'name';
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
        $query = Estudiante::query()
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

        // Map sort field to proper table
        $userFields = ['name', 'apellido', 'email', 'telefono'];
        if (in_array($this->sortField, $userFields, true)) {
            $orderBy = 'users.' . $this->sortField;
        } else {
            $orderBy = 'estudiantes.' . $this->sortField;
        }

        return $query->orderBy($orderBy, $this->sortDirection)
            ->paginate($this->perPage);
    }

    public function toggleEstado($codigoEstudiante)
    {
        $estudiante = Estudiante::where('codigo', $codigoEstudiante)->firstOrFail();
        // El modelo usa el campo 'activo' (boolean)
        $estudiante->activo = !$estudiante->activo;
        $estudiante->save();

        session()->flash('message', 'Estado del estudiante actualizado correctamente.');
    }

    public function deleteEstudiante($codigoEstudiante)
    {
        $estudiante = Estudiante::where('codigo', $codigoEstudiante)->firstOrFail();
        $estudiante->delete(); // Soft delete

        session()->flash('message', 'Estudiante eliminado correctamente.');
    }

    public function bulkDelete()
    {
        if (count($this->selected) > 0) {
            Estudiante::whereIn('codigo', $this->selected)->delete();

            $this->selected = [];
            $this->selectAll = false;

            session()->flash('message', 'Estudiantes seleccionados eliminados correctamente.');
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->estudiantes->pluck('codigo')->toArray();
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
