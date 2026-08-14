<?php

namespace App\Services;

use App\Exports\ReporteDashboardExport;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Evento;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteExportService
{
    public function __construct(
        private readonly Utf8Sanitizer $sanitizer,
    ) {
    }

    /**
     * Exporta un reporte en el formato solicitado (excel o csv).
     */
    public function exportar(
        string $tipoReporte,
        string $subtipoReporte,
        string $formatoReporte,
        ?string $cursoFiltro = null
    ): BinaryFileResponse|StreamedResponse|string|null {
        if ($formatoReporte === 'csv') {
            $rows = $this->obtenerDatosExportables($tipoReporte, $subtipoReporte, $cursoFiltro);
            $headers = $this->obtenerCabecerasExport($tipoReporte, $subtipoReporte);

            if ($rows === []) {
                return null;
            }

            return $this->descargarCsv($rows, $headers, $tipoReporte, $subtipoReporte);
        }

        // Excel: verificar que existan registros antes de generar el archivo
        $rows = $this->obtenerDatosExportables($tipoReporte, $subtipoReporte, $cursoFiltro);

        if ($rows === []) {
            return null;
        }

        // Usar FromQuery para streaming sin cargar todos los datos en memoria
        $export = new ReporteDashboardExport($tipoReporte, $subtipoReporte, $cursoFiltro);

        return Excel::download(
            $export,
            sprintf('reporte-%s-%s.xlsx', $tipoReporte, $subtipoReporte)
        );
    }

    /**
     * Obtiene la lista de cursos disponibles para el filtro de exportación.
     */
    public function obtenerCursosParaExport(): array
    {
        return Curso::query()
            ->select(['codigo', 'nombre'])
            ->orderBy('nombre')
            ->get()
            ->map(fn ($curso) => [
                'codigo' => $curso->codigo,
                'nombre' => $this->sanitizer->sanitizarTextoUtf8($curso->nombre),
            ])
            ->toArray();
    }

    private function descargarCsv(array $rows, array $headers, string $tipoReporte, string $subtipoReporte): StreamedResponse
    {
        $nombreArchivo = sprintf('reporte-%s-%s.csv', $tipoReporte, $subtipoReporte);
        $cabeceras = array_values($headers);

        return response()->streamDownload(function () use ($rows, $cabeceras) {
            $salida = fopen('php://output', 'w');

            // BOM para compatibilidad UTF-8 con Excel
            fwrite($salida, "\xEF\xBB\xBF");

            // Escribir cabeceras
            fputcsv($salida, $cabeceras);

            // Escribir filas
            foreach ($rows as $row) {
                fputcsv($salida, array_values($row));
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function obtenerCabecerasExport(string $tipoReporte, string $subtipoReporte): array
    {
        return match ($tipoReporte) {
            'cursos' => match ($subtipoReporte) {
                'gratuitos' => [
                    'codigo' => 'Código',
                    'nombre' => 'Curso',
                    'cupo_total' => 'Cupo total',
                    'inscritos' => 'Inscritos',
                    'precio_final' => 'Precio final',
                ],
                'con_ingresos' => [
                    'codigo' => 'Código',
                    'nombre' => 'Curso',
                    'estudiantes' => 'Estudiantes',
                    'ingreso_total' => 'Ingresos',
                ],
                default => [
                    'codigo' => 'Código',
                    'nombre' => 'Curso',
                    'estudiantes' => 'Estudiantes',
                    'cupo_total' => 'Cupo total',
                    'inscritos' => 'Inscritos',
                    'precio_final' => 'Precio final',
                ],
            },
            'estudiantes' => match ($subtipoReporte) {
                'por_curso' => [
                    'curso' => 'Curso',
                    'nombre' => 'Estudiante',
                    'email' => 'Email',
                    'estado_pago' => 'Estado de pago',
                    'fecha_inscripcion' => 'Fecha de inscripción',
                ],
                'cursos_terminados' => [
                    'nombre' => 'Estudiante',
                    'email' => 'Email',
                    'curso' => 'Curso',
                    'progreso' => 'Progreso',
                    'estado' => 'Estado',
                ],
                'cursos_matriculados' => [
                    'nombre' => 'Estudiante',
                    'email' => 'Email',
                    'curso' => 'Curso',
                    'estado' => 'Estado',
                    'estado_pago' => 'Estado de pago',
                ],
                default => [
                    'nombre' => 'Estudiante',
                    'email' => 'Email',
                    'activo' => 'Activo',
                    'fecha_registro' => 'Fecha de registro',
                ],
            },
            default => [
                'titulo' => 'Evento',
                'fecha' => 'Fecha',
                'ubicacion' => 'Ubicación',
                'costo' => 'Costo',
                'publicado' => 'Publicado',
            ],
        };
    }

    private function obtenerDatosExportables(string $tipoReporte, string $subtipoReporte, ?string $cursoFiltro): array
    {
        return match ($tipoReporte) {
            'cursos' => $this->obtenerDatosCursosExportables($subtipoReporte, $cursoFiltro),
            'estudiantes' => $this->obtenerDatosEstudiantesExportables($subtipoReporte, $cursoFiltro),
            default => $this->obtenerDatosEventosExportables(),
        };
    }

    private function obtenerDatosCursosExportables(string $subtipoReporte, ?string $cursoFiltro): array
    {
        $query = Curso::query()
            ->withCount('estudiantes')
            ->select(['codigo', 'nombre', 'cupo_total', 'cupo_disponible', 'precio_regular', 'precio_descuento']);

        if ($cursoFiltro) {
            $query->where('codigo', $cursoFiltro);
        }

        return $this->sanitizer->sanitizarArrayUtf8(match ($subtipoReporte) {
            'gratuitos' => $query
                ->where(function ($q) {
                    $q->where(function ($subQ) {
                        $subQ->where('precio_descuento', 0)
                            ->orWhere('precio_descuento', null)
                            ->where('precio_regular', 0);
                    })->orWhere(function ($subQ) {
                        $subQ->where('precio_descuento', 0)
                            ->where('precio_regular', 0);
                    });
                })
                ->get()
                ->map(fn ($curso) => [
                    'codigo' => $curso->codigo,
                    'nombre' => $curso->nombre,
                    'cupo_total' => $curso->cupo_total,
                    'inscritos' => $curso->inscritos_actuales,
                    'precio_final' => number_format((float) $curso->precioFinal, 2, '.', ''),
                ])
                ->toArray(),
            'con_ingresos' => $query
                ->withCount('estudiantes')
                ->get()
                ->map(fn ($curso) => [
                    'codigo' => $curso->codigo,
                    'nombre' => $curso->nombre,
                    'estudiantes' => $curso->estudiantes_count,
                    'ingreso_total' => number_format((float) $curso->precioFinal * (int) $curso->estudiantes_count, 2, '.', ''),
                ])
                ->toArray(),
            default => $query
                ->get()
                ->map(fn ($curso) => [
                    'codigo' => $curso->codigo,
                    'nombre' => $curso->nombre,
                    'estudiantes' => $curso->estudiantes_count,
                    'cupo_total' => $curso->cupo_total,
                    'inscritos' => $curso->inscritos_actuales,
                    'precio_final' => number_format((float) $curso->precioFinal, 2, '.', ''),
                ])
                ->toArray(),
        });
    }

    private function obtenerDatosEstudiantesExportables(string $subtipoReporte, ?string $cursoFiltro): array
    {
        return $this->sanitizer->sanitizarArrayUtf8(match ($subtipoReporte) {
            'por_curso' => DB::table('curso_estudiante as ce')
                ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->when($cursoFiltro, fn ($query) => $query->where('ce.curso_id', $cursoFiltro))
                ->select([
                    'c.nombre as curso',
                    'u.name as nombre',
                    'u.email as email',
                    'ce.estado_pago',
                    'ce.fecha_inscripcion',
                ])
                ->orderBy('c.nombre')
                ->get()
                ->map(fn ($row) => [
                    'curso' => $row->curso,
                    'nombre' => $row->nombre,
                    'email' => $row->email,
                    'estado_pago' => $row->estado_pago,
                    'fecha_inscripcion' => $row->fecha_inscripcion,
                ])
                ->toArray(),
            'cursos_terminados' => DB::table('curso_estudiante as ce')
                ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->when($cursoFiltro, fn ($query) => $query->where('ce.curso_id', $cursoFiltro))
                ->where('ce.progreso', '>=', 100)
                ->select([
                    'u.name as nombre',
                    'u.email as email',
                    'c.nombre as curso',
                    'ce.progreso',
                    'ce.estado',
                ])
                ->get()
                ->map(fn ($row) => [
                    'nombre' => $row->nombre,
                    'email' => $row->email,
                    'curso' => $row->curso,
                    'progreso' => $row->progreso,
                    'estado' => $row->estado,
                ])
                ->toArray(),
            'cursos_matriculados' => DB::table('curso_estudiante as ce')
                ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->when($cursoFiltro, fn ($query) => $query->where('ce.curso_id', $cursoFiltro))
                ->where('ce.estado', 'inscrito')
                ->select([
                    'u.name as nombre',
                    'u.email as email',
                    'c.nombre as curso',
                    'ce.estado',
                    'ce.estado_pago',
                ])
                ->get()
                ->map(fn ($row) => [
                    'nombre' => $row->nombre,
                    'email' => $row->email,
                    'curso' => $row->curso,
                    'estado' => $row->estado,
                    'estado_pago' => $row->estado_pago,
                ])
                ->toArray(),
            default => Estudiante::query()
                ->with('user')
                ->get()
                ->map(fn ($estudiante) => [
                    'nombre' => $estudiante->user?->name.' '.$estudiante->user?->apellido,
                    'email' => $estudiante->user?->email,
                    'activo' => $estudiante->activo ? 'Sí' : 'No',
                    'fecha_registro' => $estudiante->fecha_registro?->format('d/m/Y'),
                ])
                ->toArray(),
        });
    }

    private function obtenerDatosEventosExportables(): array
    {
        return $this->sanitizer->sanitizarArrayUtf8(Evento::query()
            ->where('publicado', true)
            ->orderBy('fecha', 'desc')
            ->get()
            ->map(fn ($evento) => [
                'titulo' => $evento->titulo,
                'fecha' => $evento->fecha?->format('d/m/Y'),
                'ubicacion' => $evento->ubicacion ?: 'Virtual',
                'costo' => number_format((float) $evento->costo, 2, '.', ''),
                'publicado' => $evento->publicado ? 'Sí' : 'No',
            ])
            ->toArray());
    }
}
