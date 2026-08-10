<?php

namespace App\Livewire\Admin\Blog;

use App\Models\Articulo;
use Livewire\Component;
use Livewire\WithPagination;

class IndexBlog extends Component
{
    use WithPagination;

    public string $search = '';

    public string $categoriaFilter = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategoriaFilter(): void
    {
        $this->resetPage();
    }

    public function togglePublicado(Articulo $articulo): void
    {
        $articulo->update([
            'publicado' => ! $articulo->publicado,
            'fecha_publicacion' => ! $articulo->publicado ? now() : $articulo->fecha_publicacion,
        ]);

        $estado = $articulo->publicado ? 'publicado' : 'despublicado';
        $this->dispatch('show-toast', type: 'success', message: "Articulo {$estado} correctamente.");
    }

    public function toggleDestacado(Articulo $articulo): void
    {
        $articulo->update(['destacado' => ! $articulo->destacado]);

        $this->dispatch('show-toast', type: 'success', message: 'Estado actualizado.');
    }

    public function eliminar(Articulo $articulo): void
    {
        $articulo->delete();

        $this->dispatch('show-toast', type: 'success', message: 'Articulo eliminado.');
    }

    public function render()
    {
        $query = Articulo::with('administrador.user')->orderByDesc('created_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%'.$this->search.'%')
                    ->orWhere('resumen', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->categoriaFilter) {
            $query->where('categoria', $this->categoriaFilter);
        }

        $categorias = Articulo::distinct()->pluck('categoria')->filter()->values();

        return view('livewire.admin.blog.index-blog', [
            'articulos' => $query->paginate(15),
            'categorias' => $categorias,
            'totalPublicados' => Articulo::where('publicado', true)->count(),
            'totalArticulos' => Articulo::count(),
        ])->layout('layouts.app');
    }
}
