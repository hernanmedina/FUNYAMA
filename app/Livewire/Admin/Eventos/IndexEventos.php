<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
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
        $evento->delete();
        
        session()->flash('message', 'Evento eliminado correctamente.');
    }

    public function togglePublicado($idEvento)
    {
        $evento = Evento::findOrFail($idEvento);
        $evento->update(['publicado' => !$evento->publicado]);
        
        $mensaje = $evento->publicado ? 'Evento publicado.' : 'Evento ocultado.';
        session()->flash('message', $mensaje);
    }

    public function toggleDestacado($idEvento)
    {
        $evento = Evento::findOrFail($idEvento);
        $evento->update(['destacado' => !$evento->destacado]);
        
        $mensaje = $evento->destacado ? 'Evento destacado.' : 'Evento sin destacar.';
        session()->flash('message', $mensaje);
    }

    public function render()
    {
        $query = Evento::query();

        if ($this->search) {
            $query->where('titulo', 'like', '%' . $this->search . '%')
                  ->orWhere('descripcion', 'like', '%' . $this->search . '%')
                  ->orWhere('ubicacion', 'like', '%' . $this->search . '%');
        }

        if ($this->filtroEstado === 'publicado') {
            $query->where('publicado', true);
        } elseif ($this->filtroEstado === 'no_publicado') {
            $query->where('publicado', false);
        } elseif ($this->filtroEstado === 'destacado') {
            $query->where('destacado', true);
        }

        $eventos = $query->orderBy('fecha', 'desc')->paginate(10);

        return view('livewire.admin.eventos.index-eventos', [
            'eventos' => $eventos,
        ]);
    }
}
