<?php

namespace App\Livewire\Estudiante;

use App\Models\Estudiante;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MostrarEstudiante extends Component
{
    public Estudiante $estudiante;

    public function mount(Estudiante $estudiante)
    {
        $this->authorize('view', $estudiante);

        $this->estudiante = $estudiante;
    }

    public function render()
    {
        return view('livewire.estudiante.mostrar-estudiante');
    }
}
