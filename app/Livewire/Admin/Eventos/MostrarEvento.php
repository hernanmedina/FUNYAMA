<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class MostrarEvento extends Component
{
    public Evento $evento;

    public function mount(Evento $evento): void
    {
        $this->authorize('view', $evento);

        $this->evento = $evento;
    }

    public function togglePublicado(): void
    {
        $this->authorize('update', $this->evento);

        $this->evento->update(['publicado' => ! $this->evento->publicado]);

        session()->flash('message', $this->evento->publicado ? 'Evento publicado.' : 'Evento ocultado.');
    }

    public function toggleDestacado(): void
    {
        $this->authorize('update', $this->evento);

        $this->evento->update(['destacado' => ! $this->evento->destacado]);

        session()->flash('message', $this->evento->destacado ? 'Evento destacado.' : 'Evento sin destacar.');
    }

    public function render()
    {
        $inscripciones = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $totalInscritos = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->count();

        $pagosPendientes = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->where('pago_realizado', false)
            ->count();

        $ingresos = (float) InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->where('pago_realizado', true)
            ->count() * (float) $this->evento->costo;

        return view('livewire.admin.eventos.mostrar-evento', [
            'inscripciones' => $inscripciones,
            'totalInscritos' => $totalInscritos,
            'pagosPendientes' => $pagosPendientes,
            'ingresos' => $ingresos,
        ]);
    }
}
