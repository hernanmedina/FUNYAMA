<?php

namespace App\Livewire\Docente;

use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class MisCursosDocente extends Component
{
    use WithPagination;

    public string $search = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Curso::query()
            ->where('instructor_id', Auth::id())
            ->withCount('estudiantes');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('codigo', 'like', '%'.$this->search.'%');
            });
        }

        $cursos = $query->orderBy('nombre')->paginate(12);

        return view('livewire.docente.mis-cursos-docente', [
            'cursos' => $cursos,
        ]);
    }
}
