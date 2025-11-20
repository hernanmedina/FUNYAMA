<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Curso;

class Cursos extends Component
{
    // defnimos las variables
    public $cursos;

    public function render()
    {
        $this->cursos = Curso::all();
        return view('livewire.cursos')
            ->layout('layouts.app');
    }
}
