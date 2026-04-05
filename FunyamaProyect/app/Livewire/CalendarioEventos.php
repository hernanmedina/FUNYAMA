<?php

namespace App\Livewire;

use App\Models\Evento;
use Livewire\Component;

class CalendarioEventos extends Component
{
    public $eventoSeleccionado = null;
    public $meses = [];
    public $mesActual = '';
    public $anioActual = '';
    public $eventosFiltrados = [];

    public function mount()
    {
        $this->mesActual = now()->month;
        $this->anioActual = now()->year;
        $this->cargarEventos();
    }

    public function cargarEventos()
    {
        $inicio = now()->startOfMonth()->subMonths(1);
        $fin = now()->addMonths(3)->endOfMonth();
        
        $this->eventosFiltrados = Evento::where('publicado', true)
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('fecha')
            ->get()
            ->toArray();
    }

    public function seleccionarEvento($id)
    {
        $this->eventoSeleccionado = Evento::find($id);
    }

    public function cerrarModal()
    {
        $this->eventoSeleccionado = null;
    }

    public function siguienteMes()
    {
        if ($this->mesActual < 12) {
            $this->mesActual++;
        } else {
            $this->mesActual = 1;
            $this->anioActual++;
        }
    }

    public function mesAnterior()
    {
        if ($this->mesActual > 1) {
            $this->mesActual--;
        } else {
            $this->mesActual = 12;
            $this->anioActual--;
        }
    }

    public function render()
    {
        $eventos = Evento::where('publicado', true)
            ->orderBy('fecha')
            ->take(20)
            ->get();

        return view('livewire.calendario-eventos', [
            'eventos' => $eventos,
        ]);
    }
}
