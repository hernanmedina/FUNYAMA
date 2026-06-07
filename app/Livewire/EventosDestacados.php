<?php

namespace App\Livewire;

use App\Models\Evento;
use Livewire\Component;
use Carbon\Carbon;

class EventosDestacados extends Component
{
    public $eventos = [];

    public function mount()
    {
        $this->cargarEventos();
    }

    public function cargarEventos()
    {
        $hoy = Carbon::now()->startOfDay();
        
        $this->eventos = Evento::where('publicado', true)
            ->whereDate('fecha', '>=', $hoy)
            ->orderBy('fecha', 'asc')
            ->limit(3)
            ->get();
    }

    public function render()
    {
        return view('livewire.eventos-destacados', [
            'eventos' => $this->eventos
        ]);
    }
}
