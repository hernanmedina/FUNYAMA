<?php

namespace App\Services;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Evento;
use App\Models\Solicitud;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardEstadisticasService
{
    public function __construct(
        private readonly Utf8Sanitizer $sanitizer,
    ) {}

    /**
     * Calcula las estadísticas del dashboard para un rango de fechas.
     *
     * @return array<string, mixed>
     */
    public function obtenerEstadisticas(int $rangoDias = 30): array
    {
        $fechaInicio = now()->subDays($rangoDias);
        $solicitudesInscripcion = Solicitud::query()->where('tipo', 'inscripcion');

        $estadisticas = [
            'total_cursos' => Curso::count(),
            'cursos_publicados' => Curso::where('publicado', true)->count(),
            'total_eventos_publicados' => Evento::where('publicado', true)->count(),
            'total_estudiantes' => Estudiante::where('activo', true)->count(),
            'total_usuarios' => User::count(),
            'solicitudes_pendientes' => (clone $solicitudesInscripcion)->where('estado', 'pendiente')->count(),
            'ingresos_totales' => $this->obtenerIngresos(),
            'matriculas_gratuitas' => (int) DB::table('curso_estudiante')
                ->where('estado_pago', 'pendiente')
                ->where('pago_realizado', 0)
                ->count(),

            // Estadísticas del período
            'nuevos_estudiantes' => Estudiante::where('created_at', '>=', $fechaInicio)->count(),
            'nuevos_cursos' => Curso::where('created_at', '>=', $fechaInicio)->count(),
            'ingresos_recientes' => $this->obtenerIngresos($fechaInicio),

            'solicitudes_resueltas' => (clone $solicitudesInscripcion)
                ->where('estado', 'resuelta')
                ->where('updated_at', '>=', $fechaInicio)
                ->count(),
        ];

        // Cursos más populares (con más estudiantes)
        try {
            $estadisticas['cursos_populares'] = $this->sanitizer->normalizarColeccionModelos(Curso::withCount('estudiantes')
                ->orderBy('estudiantes_count', 'desc')
                ->take(5)
                ->get());
        } catch (\Exception $e) {
            // Fallback si hay error con la estructura de BD
            $estadisticas['cursos_populares'] = $this->sanitizer->normalizarColeccionModelos(Curso::take(5)->get());
        }

        return $estadisticas;
    }

    /**
     * Obtiene los datos recientes del dashboard (cursos, solicitudes, estudiantes).
     *
     * @return array{cursos: mixed, solicitudes: mixed, estudiantes: mixed}
     */
    public function obtenerDatosRecientes(): array
    {
        return [
            'cursos' => $this->sanitizer->normalizarColeccionModelos(Curso::with('administrador.user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()),
            'solicitudes' => $this->sanitizer->normalizarColeccionModelos(Solicitud::with('usuario')
                ->where('tipo', 'inscripcion')
                ->where('estado', 'pendiente')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()),
            'estudiantes' => $this->sanitizer->normalizarColeccionModelos(Estudiante::with('user')
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()),
        ];
    }

    /**
     * Calcula los ingresos totales (o de un período) a partir de las matrículas.
     */
    public function obtenerIngresos(?Carbon $fechaInicio = null): float
    {
        $query = DB::table('curso_estudiante as ce')
            ->leftJoin('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->where(function ($q) {
                $q->where('ce.estado', 'inscrito')
                    ->orWhereIn('ce.estado_pago', ['completo', 'parcial']);
            })
            ->where(function ($q) {
                $q->where('ce.pago_realizado', '>', 0)
                    ->orWhereNotNull('c.precio_regular')
                    ->orWhereNotNull('c.precio_descuento');
            })
            ->selectRaw('COALESCE(SUM(
                CASE
                    WHEN ce.pago_realizado > 0 THEN ce.pago_realizado
                    WHEN c.precio_descuento IS NOT NULL THEN c.precio_descuento
                    ELSE c.precio_regular
                END
            ), 0) as total');

        if ($fechaInicio !== null) {
            $query->where('ce.created_at', '>=', $fechaInicio);
        }

        return (float) $query->value('total');
    }
}
