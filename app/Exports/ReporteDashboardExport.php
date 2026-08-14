<?php

namespace App\Exports;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReporteDashboardExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected string $tipoReporte,
        protected string $subtipoReporte,
        protected ?string $cursoFiltro = null
    ) {}

    public function query()
    {
        switch ($this->tipoReporte) {
            case 'cursos':
                return $this->cursosQuery();
            case 'estudiantes':
                return $this->estudiantesQuery();
            case 'eventos':
                return $this->eventosQuery();
            default:
                return Curso::query()->whereRaw('1 = 0'); // Empty query
        }
    }

    /**
     * Verifica si la consulta del reporte devuelve algún resultado.
     */
    public function tieneResultados(): bool
    {
        return $this->query()->exists();
    }

    public function headings(): array
    {
        return match ($this->tipoReporte) {
            'cursos' => match ($this->subtipoReporte) {
                'gratuitos' => ['Código', 'Curso', 'Cupo total', 'Inscritos', 'Precio final'],
                'con_ingresos' => ['Código', 'Curso', 'Estudiantes', 'Ingresos'],
                default => ['Código', 'Curso', 'Estudiantes', 'Cupo total', 'Inscritos', 'Precio final'],
            },
            'estudiantes' => match ($this->subtipoReporte) {
                'por_curso' => ['Curso', 'Estudiante', 'Email', 'Estado de pago', 'Fecha de inscripción'],
                'cursos_terminados' => ['Estudiante', 'Email', 'Curso', 'Progreso', 'Estado'],
                'cursos_matriculados' => ['Estudiante', 'Email', 'Curso', 'Estado', 'Estado de pago'],
                default => ['Nombre', 'Email', 'Activo', 'Fecha de registro'],
            },
            'eventos' => ['Evento', 'Fecha', 'Ubicación', 'Costo', 'Publicado'],
            default => [],
        };
    }

    public function map($row): array
    {
        switch ($this->tipoReporte) {
            case 'cursos':
                return $this->mapCurso($row);
            case 'estudiantes':
                return $this->mapEstudiante($row);
            case 'eventos':
                return $this->mapEvento($row);
            default:
                return [];
        }
    }

    private function cursosQuery()
    {
        $query = Curso::query()->withCount('estudiantes');

        if ($this->cursoFiltro) {
            $query->where('codigo', $this->cursoFiltro);
        }

        if ($this->subtipoReporte === 'gratuitos') {
            $query->where(function ($q) {
                $q->where(function ($subQ) {
                    $subQ->where('precio_descuento', 0)
                        ->orWhere('precio_descuento', null)
                        ->where('precio_regular', 0);
                })->orWhere(function ($subQ) {
                    $subQ->where('precio_descuento', 0)
                        ->where('precio_regular', 0);
                });
            });
        }

        return $query;
    }

    private function mapCurso($curso): array
    {
        return match ($this->subtipoReporte) {
            'gratuitos' => [
                $curso->codigo,
                $curso->nombre,
                $curso->cupo_total,
                $curso->inscritos_actuales,
                number_format((float) $curso->precioFinal, 2, '.', ''),
            ],
            'con_ingresos' => [
                $curso->codigo,
                $curso->nombre,
                $curso->estudiantes_count,
                number_format((float) $curso->precioFinal * (int) $curso->estudiantes_count, 2, '.', ''),
            ],
            default => [
                $curso->codigo,
                $curso->nombre,
                $curso->estudiantes_count,
                $curso->cupo_total,
                $curso->inscritos_actuales,
                number_format((float) $curso->precioFinal, 2, '.', ''),
            ],
        };
    }

    private function estudiantesQuery()
    {
        switch ($this->subtipoReporte) {
            case 'por_curso':
                return DB::table('curso_estudiante as ce')
                    ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                    ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                    ->join('users as u', 'u.id', '=', 'e.user_id')
                    ->when($this->cursoFiltro, fn ($query) => $query->where('ce.curso_id', $this->cursoFiltro))
                    ->select('c.nombre as curso_nombre', 'u.name as user_name', 'u.email', 'ce.estado_pago', 'ce.fecha_inscripcion')
                    ->orderBy('c.nombre');
            case 'cursos_terminados':
                return DB::table('curso_estudiante as ce')
                    ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                    ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                    ->join('users as u', 'u.id', '=', 'e.user_id')
                    ->when($this->cursoFiltro, fn ($query) => $query->where('ce.curso_id', $this->cursoFiltro))
                    ->where('ce.progreso', '>=', 100)
                    ->select('u.name as user_name', 'u.email', 'c.nombre as curso_nombre', 'ce.progreso', 'ce.estado');
            case 'cursos_matriculados':
                return DB::table('curso_estudiante as ce')
                    ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                    ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                    ->join('users as u', 'u.id', '=', 'e.user_id')
                    ->when($this->cursoFiltro, fn ($query) => $query->where('ce.curso_id', $this->cursoFiltro))
                    ->where('ce.estado', 'inscrito')
                    ->select('u.name as user_name', 'u.email', 'c.nombre as curso_nombre', 'ce.estado', 'ce.estado_pago');
            default: // 'total'
                return Estudiante::query()->with('user');
        }
    }

    private function mapEstudiante($row): array
    {
        switch ($this->subtipoReporte) {
            case 'por_curso':
                return [$row->curso_nombre, $row->user_name, $row->email, $row->estado_pago, $row->fecha_inscripcion];
            case 'cursos_terminados':
                return [$row->user_name, $row->email, $row->curso_nombre, $row->progreso, $row->estado];
            case 'cursos_matriculados':
                return [$row->user_name, $row->email, $row->curso_nombre, $row->estado, $row->estado_pago];
            default: // 'total'
                return [
                    $row->user?->name.' '.$row->user?->apellido,
                    $row->user?->email,
                    $row->activo ? 'Sí' : 'No',
                    $row->fecha_registro?->format('d/m/Y'),
                ];
        }
    }

    private function eventosQuery()
    {
        return Evento::query()
            ->where('publicado', true)
            ->orderBy('fecha', 'desc');
    }

    private function mapEvento($evento): array
    {
        return [
            $evento->titulo,
            $evento->fecha?->format('d/m/Y'),
            $evento->ubicacion ?: 'Virtual',
            number_format((float) $evento->costo, 2, '.', ''),
            $evento->publicado ? 'Sí' : 'No',
        ];
    }
}
