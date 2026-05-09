<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Curso;

class CursosEliminados extends Component
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

    public function getCursosEliminadosProperty()
    {
        $query = Curso::onlyTrashed()->select('cursos.*');

        if ($this->search) {
            $s = '%' . $this->search . '%';
            $query->where(function ($q) use ($s) {
                $q->where('nombre', 'like', $s)
                    ->orWhere('slug', 'like', $s)
                    ->orWhere('codigo', 'like', $s);
            });
        }

        return $query->orderBy('deleted_at', 'desc')
            ->paginate($this->perPage);
    }

    public function restaurarCurso($codigo)
    {
        $curso = Curso::withTrashed()->where('codigo', $codigo)->firstOrFail();
        $curso->restore();

        session()->flash('success', 'Curso restaurado correctamente.');
    }

    public function eliminarPermanentemente($codigo)
    {
        $curso = Curso::withTrashed()->where('codigo', $codigo)->firstOrFail();
        $curso->forceDelete();

        session()->flash('success', 'Curso eliminado permanentemente.');
    }

    public function restaurarSeleccionados()
    {
        if (count($this->selected) > 0) {
            Curso::onlyTrashed()
                ->whereIn('codigo', $this->selected)
                ->restore();

            $this->selected = [];
            $this->selectAll = false;
            $this->resetPage();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Cursos seleccionados restaurados correctamente.'
            ]);
        }
    }

    public function eliminarSeleccionadosPermanentemente()
    {
        if (count($this->selected) > 0) {
            $cursos = Curso::onlyTrashed()
                ->whereIn('codigo', $this->selected)
                ->get();

            foreach ($cursos as $curso) {
                $curso->forceDelete();
            }

            $this->selected = [];
            $this->selectAll = false;
            $this->resetPage();

            $this->dispatch('show-toast', [
                'type' => 'success',
                'message' => 'Cursos seleccionados eliminados permanentemente.'
            ]);
        }
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->cursosEliminados->pluck('codigo')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function render()
    {
        return view('livewire.admin.cursos.cursos-eliminados', [
            'cursos' => $this->cursosEliminados
        ])->layout('layouts.app');
    }
}
