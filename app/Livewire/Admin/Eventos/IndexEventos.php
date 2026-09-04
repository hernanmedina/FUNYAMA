<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Livewire\Component;
use Livewire\WithPagination;

class IndexEventos extends Component
{
    use WithPagination;

    public $search = '';

    public $filtroEstado = '';

    public $eventoAEliminar = null;

    protected $queryString = ['search', 'filtroEstado'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    public function eliminarEvento($idEvento)
    {
        $evento = Evento::findOrFail($idEvento);

        $this->authorize('delete', $evento);

        $evento->delete();

        session()->flash('message', 'Evento eliminado correctamente.');
    }

    public function togglePublicado($idEvento)
    {
        $evento = Evento::findOrFail($idEvento);

        $this->authorize('update', $evento);

        $evento->update(['publicado' => ! $evento->publicado]);

        $mensaje = $evento->publicado ? 'Evento publicado.' : 'Evento ocultado.';
        session()->flash('message', $mensaje);
    }

    public function toggleDestacado($idEvento)
    {
        $evento = Evento::findOrFail($idEvento);

        $this->authorize('update', $evento);

        $evento->update(['destacado' => ! $evento->destacado]);

        $mensaje = $evento->destacado ? 'Evento destacado.' : 'Evento sin destacar.';
        session()->flash('message', $mensaje);
    }

    public function render()
    {
        $query = Evento::query();

        if ($this->search) {
            $query->where('titulo', 'like', '%'.$this->search.'%')
                ->orWhere('descripcion', 'like', '%'.$this->search.'%')
                ->orWhere('ubicacion', 'like', '%'.$this->search.'%');
        }

        if ($this->filtroEstado === 'publicado') {
            $query->where('publicado', true);
        } elseif ($this->filtroEstado === 'no_publicado') {
            $query->where('publicado', false);
        } elseif ($this->filtroEstado === 'destacado') {
            $query->where('destacado', true);
        }

        $eventos = $query->orderBy('fecha', 'desc')->paginate(10);

        // Métricas globales de la categoría de eventos.
        $totalEventos = Evento::count();

        // Personas inscritas: inscripciones confirmadas en eventos no eliminados.
        $totalInscritos = InscripcionEvento::query()
            ->join('eventos', 'eventos.idEvento', '=', 'inscripcion_eventos.idEvento')
            ->where('inscripcion_eventos.estado', 'confirmada')
            ->whereNull('eventos.deleted_at')
            ->count();

        // Pagos pendientes: inscripciones confirmadas aún sin pagar.
        $pagosPendientes = InscripcionEvento::query()
            ->join('eventos', 'eventos.idEvento', '=', 'inscripcion_eventos.idEvento')
            ->where('inscripcion_eventos.estado', 'confirmada')
            ->where('inscripcion_eventos.pago_realizado', false)
            ->whereNull('eventos.deleted_at')
            ->count();

        // Total de ingresos recaudados por la categoría de eventos:
        // suma del costo de los eventos con inscripciones confirmadas y pagadas.
        $totalIngresos = (float) InscripcionEvento::query()
            ->join('eventos', 'eventos.idEvento', '=', 'inscripcion_eventos.idEvento')
            ->where('inscripcion_eventos.estado', 'confirmada')
            ->where('inscripcion_eventos.pago_realizado', true)
            ->whereNull('eventos.deleted_at')
            ->sum('eventos.costo');

        return view('livewire.admin.eventos.index-eventos', [
            'eventos' => $eventos,
            'totalEventos' => $totalEventos,
            'totalInscritos' => $totalInscritos,
            'pagosPendientes' => $pagosPendientes,
            'totalIngresos' => $totalIngresos,
        ]);
    }
}
