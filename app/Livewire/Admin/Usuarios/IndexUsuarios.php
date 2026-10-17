<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class IndexUsuarios extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $rolFiltro = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setRolFiltro(?string $rol): void
    {
        $this->rolFiltro = $this->rolFiltro === $rol ? null : $rol;
        $this->resetPage();
    }

    public function render()
    {
        $this->authorize('viewAny', User::class);

        $query = User::query()
            ->with(['administrador', 'docente'])
            ->whereIn('role', ['admin', 'instructor']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('documento_ID', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->rolFiltro) {
            $query->where('role', $this->rolFiltro);
        }

        $usuarios = $query->orderBy('name')->paginate(15);

        return view('livewire.admin.usuarios.index-usuarios', [
            'usuarios' => $usuarios,
        ]);
    }
}
