<?php

namespace App\Livewire;

use App\Models\Articulo;
use Livewire\Component;
use Livewire\WithPagination;

class Blog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoria = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoria(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Articulo::publicados()->recientes()->with('administrador.user');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%'.$this->search.'%')
                    ->orWhere('resumen', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->categoria) {
            $query->where('categoria', $this->categoria);
        }

        $categorias = Articulo::publicados()->distinct()->pluck('categoria')->filter()->values();

        $destacados = Articulo::publicados()->destacados()->recientes()->take(3)->get();

        return view('livewire.blog', [
            'articulos' => $query->paginate(9),
            'categorias' => $categorias,
            'destacados' => $destacados,
        ])->layout('layouts.public');
    }
}
