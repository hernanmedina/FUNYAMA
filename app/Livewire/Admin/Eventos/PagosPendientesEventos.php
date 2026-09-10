<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class PagosPendientesEventos extends Component
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

    /**
     * Marca o desmarca el pago de una inscripción.
     */
    public function togglePago(int $idInscripcion): void
    {
        $inscripcion = InscripcionEvento::findOrFail($idInscripcion);

        $this->authorize('update', $inscripcion);

        $inscripcion->update([
            'pago_realizado' => ! $inscripcion->pago_realizado,
        ]);

        $estado = $inscripcion->pago_realizado ? 'Pagado' : 'Pendiente';

        $this->dispatch('show-toast', type: 'success', message: "Inscripción marcada como: {$estado}.");
    }

    public function render()
    {
        $query = InscripcionEvento::with(['evento', 'usuario'])
            ->where('estado', 'confirmada')
            ->where('pago_realizado', false)
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

        $inscripciones = $query->orderByDesc('created_at')->paginate(15);

        // Monto total pendiente de recaudo (solo eventos con costo).
        $montoPendiente = (float) InscripcionEvento::query()
            ->join('eventos', 'eventos.idEvento', '=', 'inscripcion_eventos.idEvento')
            ->where('inscripcion_eventos.estado', 'confirmada')
            ->where('inscripcion_eventos.pago_realizado', false)
            ->whereNull('eventos.deleted_at')
            ->sum('eventos.costo');

        // Eventos con al menos un pago pendiente (para el filtro).
        $eventosConPendientes = Evento::whereHas('inscripciones', function ($q) {
            $q->where('estado', 'confirmada')->where('pago_realizado', false);
        })
            ->orderBy('titulo')
            ->get(['idEvento', 'titulo']);

        return view('livewire.admin.eventos.pagos-pendientes-eventos', [
            'inscripciones' => $inscripciones,
            'montoPendiente' => $montoPendiente,
            'eventosConPendientes' => $eventosConPendientes,
        ]);
    }
}
