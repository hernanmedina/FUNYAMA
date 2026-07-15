<?php

namespace App\Livewire;

use App\Models\Curso;
use Livewire\Component;

class CursosDestacados extends Component
{
    public $cursos = [];

    public function mount()
    {
        $this->cargarCursos();
    }

    public function cargarCursos()
    {
        $this->cursos = Curso::where('publicado', true)
            ->orderBy('destacado', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
    }

    public function render()
    {
        return view('livewire.cursos-destacados', [
            'cursos' => $this->cursos
        ]);
    }
}
