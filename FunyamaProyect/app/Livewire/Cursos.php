<?php
// app/Livewire/Cursos.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Curso;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')] // Usar el layout público
class Cursos extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 9;

    public function render()
    {
        $cursos = Curso::where('publicado', true)
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.cursos', [
            'cursos' => $cursos
        ]);
    }
}
