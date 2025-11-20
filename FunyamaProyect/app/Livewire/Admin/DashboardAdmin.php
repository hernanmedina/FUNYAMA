<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\Solicitud;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class DashboardAdmin extends Component
{
    use WithPagination;

    public $estadisticas;
    public $cursosRecientes;
    public $solicitudesPendientes;
    public $estudiantesRecientes;

    // Filtros para estadísticas
    public $rangoFechas = '30'; // 7, 30, 90, 365 días

    protected $queryString = [
        'rangoFechas' => ['except' => '30'],
    ];

    public function mount()
    {
        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();
    }

    public function updatedRangoFechas()
    {
        $this->cargarEstadisticas();
    }

    private function cargarEstadisticas()
    {
        $fechaInicio = now()->subDays($this->rangoFechas);

        // CORREGIDO: Usar DB::raw para calcular ingresos en lugar del modelo CursoEstudiante
        $this->estadisticas = [
            'total_cursos' => Curso::count(),
            'cursos_publicados' => Curso::where('publicado', true)->count(),
            'total_estudiantes' => Estudiante::where('activo', true)->count(),
            'total_usuarios' => User::count(),
            'solicitudes_pendientes' => Solicitud::where('estado', 'pendiente')->count(),

            // CORREGIDO: Calcular ingresos usando DB query
            'ingresos_totales' => \DB::table('curso_estudiante')->sum('pago_realizado'),

            // Estadísticas del período
            'nuevos_estudiantes' => Estudiante::where('created_at', '>=', $fechaInicio)->count(),
            'nuevos_cursos' => Curso::where('created_at', '>=', $fechaInicio)->count(),

            // CORREGIDO: Calcular ingresos recientes usando DB query
            'ingresos_recientes' => \DB::table('curso_estudiante')
                ->where('created_at', '>=', $fechaInicio)
                ->sum('pago_realizado'),

            'solicitudes_resueltas' => Solicitud::where('estado', 'resuelta')
                ->where('updated_at', '>=', $fechaInicio)
                ->count(),
        ];

        // Cursos más populares (con más estudiantes)
        $this->estadisticas['cursos_populares'] = Curso::withCount('estudiantes')
            ->orderBy('estudiantes_count', 'desc')
            ->take(5)
            ->get();
    }

    private function cargarDatosRecientes()
    {
        $this->cursosRecientes = Curso::with('administrador.user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $this->solicitudesPendientes = Solicitud::with('usuario')
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $this->estudiantesRecientes = Estudiante::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
    }

    public function eliminarCurso($cursoId)
    {
        $curso = Curso::findOrFail($cursoId);

        if ($curso->estudiantes()->count() > 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No se puede eliminar el curso porque tiene estudiantes inscritos.'
            );
            return;
        }

        $curso->delete();
        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Curso eliminado correctamente.'
        );
    }

    public function togglePublicacion($cursoId)
    {
        $curso = Curso::findOrFail($cursoId);
        $curso->update(['publicado' => !$curso->publicado]);

        $action = $curso->publicado ? 'publicado' : 'ocultado';
        $this->dispatch('show-toast',
            type: 'success',
            message: "Curso {$action} correctamente."
        );

        $this->cargarEstadisticas();
    }

    public function marcarSolicitudResuelta($solicitudId)
    {
        $solicitud = Solicitud::findOrFail($solicitudId);
        $solicitud->update([
            'estado' => 'resuelta',
            'fecha_respuesta' => now(),
            'atendido_por_admin' => auth()->user()->administrador->idAdmin ?? null
        ]);

        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Solicitud marcada como resuelta.'
        );
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin')
            ->layout('layouts.app');
    }
}
