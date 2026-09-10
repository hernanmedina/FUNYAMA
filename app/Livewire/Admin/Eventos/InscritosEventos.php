<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class InscritosEventos extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroEvento = '';

    public string $filtroPago = '';

    protected $queryString = ['search', 'filtroEvento', 'filtroPago'];

    public function mount(): void
    {
        $this->authorize('viewAny', Evento::class);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEvento(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroPago(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = InscripcionEvento::with(['evento', 'usuario'])
            ->where('estado', 'confirmada')
            ->whereHas('evento', function ($q) {
                $q->whereNull('deleted_at');
            });

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('documento', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filtroEvento) {
            $query->where('idEvento', $this->filtroEvento);
        }

        if ($this->filtroPago === 'pagado') {
            $query->where('pago_realizado', true);
        } elseif ($this->filtroPago === 'pendiente') {
            $query->where('pago_realizado', false);
        }

        $inscripciones = $query->orderByDesc('created_at')->paginate(15);

        // Eventos con al menos una inscripción confirmada (para el filtro).
        $eventosConInscritos = Evento::whereHas('inscripciones', function ($q) {
            $q->where('estado', 'confirmada');
        })
            ->orderBy('titulo')
            ->get(['idEvento', 'titulo']);

        return view('livewire.admin.eventos.inscritos-eventos', [
            'inscripciones' => $inscripciones,
            'eventosConInscritos' => $eventosConInscritos,
        ]);
    }
}
