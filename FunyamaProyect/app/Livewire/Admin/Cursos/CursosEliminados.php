<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use App\Models\Curso;

class CursosEliminados extends Component
{
    public function restaurarCurso($cursoId)
    {
        $curso = Curso::withTrashed()->findOrFail($cursoId);
        $curso->restore();

        session()->flash('success', 'Curso restaurado correctamente.');
    }

    public function eliminarPermanentemente($cursoId)
    {
        $curso = Curso::withTrashed()->findOrFail($cursoId);
        $curso->forceDelete();

        session()->flash('success', 'Curso eliminado permanentemente.');
    }

    public function render()
    {
        $cursosEliminados = Curso::onlyTrashed()->get();

        return view('livewire.admin.cursos.cursos-eliminados', [
            'cursosEliminados' => $cursosEliminados
        ])->layout('layouts.app');
    }
}
