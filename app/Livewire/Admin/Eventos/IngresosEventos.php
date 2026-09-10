<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class IngresosEventos extends Component
{
    use WithPagination;

    public string $search = '';

    public string $filtroEvento = '';

    protected $queryString = ['search', 'filtroEvento'];

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

    public function render()
    {
        $query = InscripcionEvento::with(['evento', 'usuario'])
            ->where('estado', 'confirmada')
            ->where('pago_realizado', true)
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

        $inscripciones = $query->orderByDesc('updated_at')->paginate(15);

        // Total recaudado (solo eventos con costo).
        $totalRecaudado = (float) InscripcionEvento::query()
            ->join('eventos', 'eventos.idEvento', '=', 'inscripcion_eventos.idEvento')
            ->where('inscripcion_eventos.estado', 'confirmada')
            ->where('inscripcion_eventos.pago_realizado', true)
            ->whereNull('eventos.deleted_at')
            ->sum('eventos.costo');

        // Eventos con al menos un pago registrado (para el filtro).
        $eventosConIngresos = Evento::whereHas('inscripciones', function ($q) {
            $q->where('estado', 'confirmada')->where('pago_realizado', true);
        })
            ->orderBy('titulo')
            ->get(['idEvento', 'titulo']);

        return view('livewire.admin.eventos.ingresos-eventos', [
            'inscripciones' => $inscripciones,
            'totalRecaudado' => $totalRecaudado,
            'eventosConIngresos' => $eventosConIngresos,
        ]);
    }
}
