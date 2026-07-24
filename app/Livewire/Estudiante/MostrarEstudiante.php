<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Estudiante;

#[Layout('layouts.app')]
class MostrarEstudiante extends Component
{
    public Estudiante $estudiante;

    public function mount(Estudiante $estudiante)
    {
        $this->estudiante = $estudiante;
    }

    public function render()
    {
        return view('livewire.estudiante.mostrar-estudiante');
    }
}
