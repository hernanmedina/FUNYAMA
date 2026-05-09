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
        $now = now();

        $this->mesActual = $now->month;
        $this->anioActual = $now->year;

        $this->cargarEventos();
    }

    public function cargarEventos()
{
    $inicio = \Carbon\Carbon::create($this->anioActual, $this->mesActual, 1)
        ->startOfMonth()
        ->startOfDay();

    $fin = \Carbon\Carbon::create($this->anioActual, $this->mesActual, 1)
        ->endOfMonth()
        ->endOfDay();

    $this->eventosFiltrados = Evento::where('publicado', true)
        ->whereBetween('fecha', [$inicio, $fin])
        ->orderBy('fecha')
        ->get();
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
        $fecha = \Carbon\Carbon::create($this->anioActual, $this->mesActual, 1)
            ->addMonth();

        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;

        $this->cargarEventos();
    }

    public function mesAnterior()
    {
        $fecha = \Carbon\Carbon::create($this->anioActual, $this->mesActual, 1)
            ->subMonth();

        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;

        $this->cargarEventos();
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
