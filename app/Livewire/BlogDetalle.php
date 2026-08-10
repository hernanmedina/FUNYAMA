<?php

namespace App\Livewire;

use App\Models\Articulo;
use Livewire\Component;

class BlogDetalle extends Component
{
    public Articulo $articulo;

    public function mount(Articulo $articulo): void
    {
        abort_unless($articulo->esta_publicado, 404);

        $articulo->incrementarVistas();
    }

    public function render()
    {
        $relacionados = Articulo::publicados()
            ->where('idPost', '!=', $this->articulo->idPost)
            ->where('categoria', $this->articulo->categoria)
            ->recientes()
            ->take(3)
            ->get();

        return view('livewire.blog-detalle', [
            'relacionados' => $relacionados,
        ])->layout('layouts.public');
    }
}
