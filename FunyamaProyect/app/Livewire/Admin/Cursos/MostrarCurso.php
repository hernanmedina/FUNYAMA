<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use App\Models\Curso;

class MostrarCurso extends Component
{
    public Curso $curso;

    public function mount(Curso $curso)
    {
        $this->curso = $curso;
    }

    public function eliminarCurso()
    {
        // Verificar si hay estudiantes inscritos
        if ($this->curso->estudiantes()->count() > 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No se puede eliminar el curso porque tiene estudiantes inscritos.'
            );
            return;
        }

        $this->curso->delete();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Curso eliminado correctamente.'
        );

        return redirect()->route('admin.cursos.index');
    }

    public function togglePublicacion()
    {
        $this->curso->update(['publicado' => !$this->curso->publicado]);

        $action = $this->curso->publicado ? 'publicado' : 'ocultado';
        $this->dispatch('show-toast',
            type: 'success',
            message: "Curso {$action} correctamente."
        );
    }

    public function render()
    {
        return view('livewire.admin.cursos.mostrar-curso', [
            'estudiantes' => $this->curso->estudiantes()->with('user')->get(),
        ])->layout('layouts.app');
    }
}
