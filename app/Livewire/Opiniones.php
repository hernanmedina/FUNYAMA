<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Opiniones extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $ratingFilter = null;

    public string $sortBy = 'fecha';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedRatingFilter(): void
    {
        $this->resetPage();
    }

    public function setRatingFilter(?int $rating): void
    {
        $this->ratingFilter = $this->ratingFilter === $rating ? null : $rating;
        $this->resetPage();
    }

    public function render()
    {
        $query = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->whereNotNull('ce.opinion_estudiante')
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('c.nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('u.name', 'like', '%'.$this->search.'%')
                    ->orWhere('u.apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('ce.opinion_estudiante', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->ratingFilter) {
            $query->where('ce.rating_estudiante', $this->ratingFilter);
        }

        $sortColumn = match ($this->sortBy) {
            'rating' => 'ce.rating_estudiante',
            'curso' => 'c.nombre',
            default => 'ce.updated_at',
        };

        $opiniones = $query
            ->select(
                'c.codigo as curso_id',
                'c.nombre as curso_nombre',
                'u.name',
                'u.apellido',
                'ce.rating_estudiante',
                'ce.opinion_estudiante',
                'ce.updated_at'
            )
            ->orderBy($sortColumn, 'desc')
            ->paginate(9);

        $totalOpiniones = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->whereNotNull('ce.opinion_estudiante')
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at')
            ->count();

        $promedioRating = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->whereNotNull('ce.rating_estudiante')
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at')
            ->avg('ce.rating_estudiante');

        return view('livewire.opiniones', [
            'opiniones' => $opiniones,
            'totalOpiniones' => $totalOpiniones,
            'promedioRating' => round((float) $promedioRating, 1),
        ])->layout('layouts.public');
    }
}
