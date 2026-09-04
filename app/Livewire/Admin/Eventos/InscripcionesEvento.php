<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Livewire\Component;
use Livewire\WithPagination;

class InscripcionesEvento extends Component
{
    use WithPagination;

    public Evento $evento;

    public string $search = '';

    public string $filtroEstado = '';

    public function mount(Evento $evento)
    {
        $this->evento = $evento;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    /**
     * Cancela una inscripción (libera el cupo).
     */
    public function cancelarInscripcion($idInscripcion)
    {
        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('idEvento', $this->evento->idEvento)
            ->firstOrFail();

        $this->authorize('update', $inscripcion);

        if ($inscripcion->estado === 'cancelada') {
            session()->flash('message', 'Esta inscripción ya estaba cancelada.');

            return;
        }

        $inscripcion->update(['estado' => 'cancelada']);

        // Liberar el cupo
        $this->evento->decrement('inscritos_actual');

        session()->flash('message', 'Inscripción cancelada correctamente.');
    }

    /**
     * Reactiva una inscripción cancelada (ocupa el cupo si hay disponibilidad).
     */
    public function reactivarInscripcion($idInscripcion)
    {
        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('idEvento', $this->evento->idEvento)
            ->firstOrFail();

        $this->authorize('update', $inscripcion);

        if ($inscripcion->estado === 'confirmada') {
            session()->flash('message', 'Esta inscripción ya estaba confirmada.');

            return;
        }

        // Verificar disponibilidad de cupo
        if ($this->evento->cupo_maximo !== null && $this->evento->inscritos_actual >= $this->evento->cupo_maximo) {
            session()->flash('error', 'No hay cupos disponibles para reactivar esta inscripción.');

            return;
        }

        $inscripcion->update(['estado' => 'confirmada']);

        // Ocupar el cupo
        $this->evento->increment('inscritos_actual');

        session()->flash('message', 'Inscripción reactivada correctamente.');
    }

    /**
     * Marca o desmarca el pago de una inscripción (checkbox).
     */
    public function togglePago($idInscripcion)
    {
        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('idEvento', $this->evento->idEvento)
            ->firstOrFail();

        $this->authorize('update', $inscripcion);

        $inscripcion->update([
            'pago_realizado' => ! $inscripcion->pago_realizado,
        ]);

        $estado = $inscripcion->pago_realizado ? 'Pagado' : 'Pendiente';

        $this->dispatch('show-toast', type: 'success', message: "Inscripción marcada como: {$estado}.");
    }

    public function render()
    {
        $query = InscripcionEvento::with('usuario')
            ->where('idEvento', $this->evento->idEvento);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('documento', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        $inscripciones = $query->orderByDesc('created_at')->paginate(15);

        // Resumen de pagos (solo inscripciones confirmadas)
        $pagados = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->where('pago_realizado', true)
            ->count();

        $pendientes = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->where('pago_realizado', false)
            ->count();

        // Total recaudado por los pagos confirmados
        $totalRecaudado = $pagados * (float) $this->evento->costo;

        return view('livewire.admin.eventos.inscripciones-evento', [
            'inscripciones' => $inscripciones,
            'pagados' => $pagados,
            'pendientes' => $pendientes,
            'totalRecaudado' => $totalRecaudado,
        ]);
    }
}
