<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')] 

class DashboardEstudiante extends Component
{
    public $cursosInscritos;
    public $cursosDisponibles;
    public $estadisticas;

    public function mount()
    {
        $user = Auth::user();
        $estudiante = $user->estudiante;

        if ($estudiante) {
            // Cursos en los que está inscrito el estudiante
            $this->cursosInscritos = $estudiante->cursos()
                ->withPivot('estado', 'progreso', 'fecha_inscripcion')
                ->orderBy('curso_estudiante.fecha_inscripcion', 'desc')
                ->take(5)
                ->get();

            // Cursos disponibles (no inscritos y publicados)
            $cursosInscritosIds = $estudiante->cursos()->pluck('cursos.idCurso');
            $this->cursosDisponibles = Curso::where('publicado', true)
                ->whereNotIn('idCurso', $cursosInscritosIds)
                ->where('cupo_disponible', '>', 0)
                ->where('fecha_inicio', '>=', now())
                ->orderBy('fecha_inicio', 'asc')
                ->take(6)
                ->get();

            // Estadísticas
            $this->estadisticas = [
                'total_cursos' => $estudiante->cursos()->count(),
                'cursos_completados' => $estudiante->cursos()->wherePivot('estado', 'completado')->count(),
                'cursos_en_progreso' => $estudiante->cursos()->wherePivot('estado', 'en_progreso')->count(),
                'promedio_progreso' => $estudiante->cursos()->avg('curso_estudiante.progreso') ?? 0,
            ];
        }
    }

    public function inscribirCurso($cursoId)
    {
        $estudiante = Auth::user()->estudiante;
        $curso = Curso::find($cursoId);

        if (!$curso) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Curso no encontrado.'
            );
            return;
        }

        if ($curso->cupo_disponible <= 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No hay cupos disponibles en este curso.'
            );
            return;
        }

        // Verificar si ya está inscrito
        if ($estudiante->cursos()->where('curso_id', $cursoId)->exists()) {
            $this->dispatch('show-toast',
                type: 'warning',
                message: 'Ya estás inscrito en este curso.'
            );
            return;
        }

        try {
            // Inscribir al estudiante
            $estudiante->cursos()->attach($cursoId, [
                'estado' => 'inscrito',
                'fecha_inscripcion' => now(),
                'pago_realizado' => $curso->precioFinal,
                'estado_pago' => 'pendiente'
            ]);

            // Actualizar cupo disponible
            $curso->decrement('cupo_disponible');

            $this->dispatch('show-toast',
                type: 'success',
                message: '¡Inscripción exitosa! Ahora estás inscrito en el curso.'
            );

            // Refrescar datos
            $this->mount();

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Error al inscribirse: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.estudiante.dashboard-estudiante')
            ->layout('layouts.app');
    }
}
