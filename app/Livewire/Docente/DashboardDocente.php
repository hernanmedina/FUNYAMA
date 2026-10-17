<?php

namespace App\Livewire\Docente;

use App\Models\Asistencia;
use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class DashboardDocente extends Component
{
    public function render()
    {
        $user = Auth::user();

        $cursos = Curso::query()
            ->where('instructor_id', $user->id)
            ->withCount('estudiantes')
            ->orderBy('nombre')
            ->get();

        $totalEstudiantes = $cursos->sum('estudiantes_count');

        $totalRecursos = $cursos->sum(function (Curso $curso) {
            return $curso->recursos()->count();
        });

        $asistenciasRegistradas = Asistencia::query()
            ->whereIn('curso_id', $cursos->pluck('codigo'))
            ->count();

        $estadisticas = [
            'total_cursos' => $cursos->count(),
            'total_estudiantes' => $totalEstudiantes,
            'total_recursos' => $totalRecursos,
            'asistencias_registradas' => $asistenciasRegistradas,
        ];

        return view('livewire.docente.dashboard-docente', [
            'cursos' => $cursos,
            'cursosImpartidos' => $cursos,
            'estadisticas' => $estadisticas,
        ]);
    }
}
