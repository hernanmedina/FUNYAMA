<?php

namespace App\Livewire\Estudiante;

use App\Models\InscripcionEvento;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MisEventos extends Component
{
    use WithPagination;

    public string $tab = 'proximos';

    public string $search = '';

    public function updatedTab()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    #[Computed]
    public function misInscripciones()
    {
        $user = Auth::user();

        if (! $user) {
            return collect();
        }

        $query = InscripcionEvento::with('evento')
            ->where('user_id', $user->id)
            ->where('estado', 'confirmada');

        // Aplicar búsqueda
        if ($this->search) {
            $query->whereHas('evento', function ($q) {
                $q->where('titulo', 'like', '%'.$this->search.'%');
            });
        }

        // Filtrar por pestaña (próximos vs pasados)
        if ($this->tab === 'pasados') {
            $query->whereHas('evento', function ($q) {
                $q->where('fecha', '<', now());
            });
        } else {
            $query->whereHas('evento', function ($q) {
                $q->where('fecha', '>=', now());
            });
        }

        return $query->orderByDesc('created_at')->paginate(9);
    }

    /**
     * Cancela la inscripción de un evento (libera el cupo).
     */
    public function cancelarInscripcion($idInscripcion)
    {
        $user = Auth::user();

        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('user_id', $user->id)
            ->where('estado', 'confirmada')
            ->first();

        if (! $inscripcion) {
            $this->dispatch('show-toast', type: 'error', message: 'Inscripción no encontrada.');

            return;
        }

        // Verificar que el evento aún no haya pasado
        if ($inscripcion->evento && $inscripcion->evento->fecha < now()) {
            $this->dispatch('show-toast', type: 'warning', message: 'No puedes cancelar la inscripción de un evento que ya finalizó.');

            return;
        }

        $inscripcion->update(['estado' => 'cancelada']);

        // Liberar el cupo
        if ($inscripcion->evento) {
            $inscripcion->evento->decrement('inscritos_actual');
        }

        $this->dispatch('show-toast', type: 'success', message: 'Tu inscripción al evento fue cancelada.');
    }

    public function render()
    {
        return view('livewire.estudiante.mis-eventos');
    }
}
