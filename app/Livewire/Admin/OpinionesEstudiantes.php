<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class OpinionesEstudiantes extends Component
{
    use WithPagination;

    public string $search = '';

    public ?int $ratingFilter = null;

    public string $sortBy = 'fecha';

    public string $sortDirection = 'desc';

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
            ->where(function ($q) {
                $q->whereNotNull('ce.opinion_estudiante')
                    ->orWhereNotNull('ce.rating_estudiante');
            })
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at');

        // Filtro por búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('c.nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('u.name', 'like', '%'.$this->search.'%')
                    ->orWhere('u.apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('ce.opinion_estudiante', 'like', '%'.$this->search.'%');
            });
        }

        // Filtro por rating
        if ($this->ratingFilter) {
            $query->where('ce.rating_estudiante', $this->ratingFilter);
        }

        // Ordenamiento
        $sortColumn = match ($this->sortBy) {
            'rating' => 'ce.rating_estudiante',
            'curso' => 'c.nombre',
            'estudiante' => 'u.name',
            default => 'ce.updated_at',
        };

        $opiniones = $query
            ->select(
                'c.codigo as curso_id',
                'c.nombre as curso_nombre',
                'e.codigo as estudiante_id',
                'u.name',
                'u.apellido',
                'ce.rating_estudiante',
                'ce.opinion_estudiante',
                'ce.updated_at'
            )
            ->orderBy($sortColumn, $this->sortDirection)
            ->paginate(15);

        // Estadísticas rápidas
        $totalOpiniones = $query->count();
        $promedioRating = DB::table('curso_estudiante as ce')
            ->whereNotNull('ce.rating_estudiante')
            ->avg('ce.rating_estudiante');

        return view('livewire.admin.opiniones-estudiantes', [
            'opiniones' => $opiniones,
            'totalOpiniones' => $totalOpiniones,
            'promedioRating' => round((float) $promedioRating, 1),
        ])->layout('layouts.app');
    }

    private function sanitizarTextoUtf8(?string $texto): string
    {
        if ($texto === null || $texto === '') {
            return '';
        }

        if (! mb_check_encoding($texto, 'UTF-8')) {
            $texto = mb_convert_encoding($texto, 'UTF-8', 'auto');
        }

        $texto = preg_replace('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/u', '', $texto);

        return $texto ?? '';
    }
}
