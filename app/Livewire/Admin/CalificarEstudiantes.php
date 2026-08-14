<?php

namespace App\Livewire\Admin;

use App\Exports\CalificacionesExport;
use App\Models\Curso;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CalificarEstudiantes extends Component
{
    use WithPagination;

    // ─── Formulario de calificación ───
    public ?string $cursoCalificarId = null;

    public ?string $estudianteCalificarId = null;

    public ?float $notaCalificacion = null;

    public string $retroalimentacion = '';

    public array $cursosConEstudiantes = [];

    public array $estudiantesParaCalificar = [];

    // ─── Filtros para lista de calificados ───
    public string $search = '';

    public ?string $cursoFiltro = null;

    public ?string $estadoFiltro = null;

    public ?string $rangoNota = null;

    public array $cursosParaFiltro = [];

    public function mount(): void
    {
        $this->cargarCursosConEstudiantes();
        $this->cargarCursosParaFiltro();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCursoFiltro(): void
    {
        $this->resetPage();
    }

    public function updatedEstadoFiltro(): void
    {
        $this->resetPage();
    }

    public function updatedRangoNota(): void
    {
        $this->resetPage();
    }

    public function limpiarFiltros(): void
    {
        $this->reset(['search', 'cursoFiltro', 'estadoFiltro', 'rangoNota']);
        $this->resetPage();
    }

    private function cargarCursosConEstudiantes(): void
    {
        $this->cursosConEstudiantes = Curso::query()
            ->whereHas('estudiantes', function ($q) {
                $q->whereIn('curso_estudiante.estado', ['completado', 'en_progreso']);
            })
            ->orderBy('nombre')
            ->get(['codigo', 'nombre'])
            ->map(fn ($c) => ['codigo' => $c->codigo, 'nombre' => $c->nombre])
            ->toArray();
    }

    private function cargarCursosParaFiltro(): void
    {
        $this->cursosParaFiltro = Curso::query()
            ->whereHas('estudiantes', function ($q) {
                $q->whereNotNull('curso_estudiante.calificacion');
            })
            ->orderBy('nombre')
            ->get(['codigo', 'nombre'])
            ->map(fn ($c) => ['codigo' => $c->codigo, 'nombre' => $c->nombre])
            ->toArray();
    }

    public function updatedCursoCalificarId(?string $value): void
    {
        $this->estudianteCalificarId = null;
        $this->estudiantesParaCalificar = [];

        if (! $value) {
            return;
        }

        $this->estudiantesParaCalificar = DB::table('curso_estudiante as ce')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->where('ce.curso_id', $value)
            ->whereIn('ce.estado', ['completado', 'en_progreso'])
            ->select(
                'e.codigo as estudiante_id',
                'u.name',
                'u.apellido',
                'ce.estado',
                'ce.calificacion',
                'ce.comentario_calificacion'
            )
            ->orderBy('u.name')
            ->get()
            ->map(function ($row) {
                return [
                    'estudiante_id' => $row->estudiante_id,
                    'nombre_completo' => $row->name.' '.$row->apellido,
                    'estado' => $row->estado,
                    'calificacion_actual' => $row->calificacion,
                    'retroalimentacion_actual' => $row->comentario_calificacion,
                ];
            })
            ->toArray();
    }

    public function updatedEstudianteCalificarId(?string $value): void
    {
        $this->notaCalificacion = null;
        $this->retroalimentacion = '';

        if (! $value) {
            return;
        }

        $encontrado = collect($this->estudiantesParaCalificar)
            ->firstWhere('estudiante_id', $value);

        if ($encontrado) {
            $this->notaCalificacion = $encontrado['calificacion_actual'] !== null
                ? (float) $encontrado['calificacion_actual']
                : null;
            $this->retroalimentacion = $encontrado['retroalimentacion_actual'] ?? '';
        }
    }

    public function guardarCalificacion(): void
    {
        $this->authorize('update', Curso::class);

        $this->validate([
            'cursoCalificarId' => ['required', 'string', 'exists:cursos,codigo'],
            'estudianteCalificarId' => ['required', 'string', 'exists:estudiantes,codigo'],
            'notaCalificacion' => ['required', 'numeric', 'min:0', 'max:10'],
            'retroalimentacion' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::table('curso_estudiante')
            ->where('curso_id', $this->cursoCalificarId)
            ->where('estudiante_id', $this->estudianteCalificarId)
            ->update([
                'calificacion' => $this->notaCalificacion,
                'comentario_calificacion' => $this->retroalimentacion,
                'updated_at' => now(),
            ]);

        // Refrescar datos
        $this->cargarCursosParaFiltro();
        $this->updatedCursoCalificarId($this->cursoCalificarId);

        $this->dispatch('show-toast', type: 'success', message: 'Calificación y retroalimentación guardadas correctamente.');
    }

    // ─── Exportación ────────────────────────────────────────────────────

    public function exportarExcel()
    {
        $datos = $this->obtenerDatosExportables();

        if ($datos['rows'] === []) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay estudiantes calificados para exportar con los filtros seleccionados.');

            return;
        }

        return Excel::download(
            new CalificacionesExport($datos['rows'], $datos['headings']),
            sprintf('calificaciones-%s.xlsx', now()->format('Y-m-d'))
        );
    }

    public function exportarCsv(): ?StreamedResponse
    {
        $datos = $this->obtenerDatosExportables();

        if ($datos['rows'] === []) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay estudiantes calificados para exportar con los filtros seleccionados.');

            return null;
        }

        $nombreArchivo = sprintf('calificaciones-%s.csv', now()->format('Y-m-d'));
        $cabeceras = array_values($datos['headings']);

        return response()->streamDownload(function () use ($datos, $cabeceras) {
            $salida = fopen('php://output', 'w');

            // BOM para compatibilidad UTF-8 con Excel
            fwrite($salida, "\xEF\xBB\xBF");

            // Escribir cabeceras
            fputcsv($salida, $cabeceras);

            // Escribir filas
            foreach ($datos['rows'] as $row) {
                fputcsv($salida, array_values($row));
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function obtenerDatosExportables(): array
    {
        $query = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->whereNotNull('ce.calificacion')
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('c.nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('u.name', 'like', '%'.$this->search.'%')
                    ->orWhere('u.apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('e.codigo', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->cursoFiltro) {
            $query->where('ce.curso_id', $this->cursoFiltro);
        }

        if ($this->estadoFiltro) {
            $query->where('ce.estado', $this->estadoFiltro);
        }

        if ($this->rangoNota) {
            match ($this->rangoNota) {
                'excelente' => $query->whereBetween('ce.calificacion', [9, 10]),
                'bueno' => $query->whereBetween('ce.calificacion', [7, 8.9]),
                'regular' => $query->whereBetween('ce.calificacion', [5, 6.9]),
                'insuficiente' => $query->where('ce.calificacion', '<', 5),
                default => null,
            };
        }

        $registros = $query
            ->select([
                'e.codigo as estudiante_codigo',
                'u.name',
                'u.apellido',
                'c.nombre as curso_nombre',
                'ce.estado',
                'ce.calificacion',
                'ce.comentario_calificacion',
                'ce.fecha_completado',
            ])
            ->orderByDesc('ce.updated_at')
            ->get();

        $rows = $registros->map(function ($row) {
            return [
                'estudiante_codigo' => $row->estudiante_codigo,
                'nombre_completo' => $row->name.' '.$row->apellido,
                'curso' => $row->curso_nombre,
                'estado' => $row->estado === 'completado' ? 'Completado' : 'En progreso',
                'calificacion' => number_format((float) $row->calificacion, 1),
                'retroalimentacion' => $row->comentario_calificacion ?? '',
                'fecha_completado' => $row->fecha_completado ? Carbon::parse($row->fecha_completado)->format('d/m/Y') : '',
            ];
        })->toArray();

        $headings = [
            'estudiante_codigo' => 'Código Estudiante',
            'nombre_completo' => 'Estudiante',
            'curso' => 'Curso',
            'estado' => 'Estado',
            'calificacion' => 'Nota',
            'retroalimentacion' => 'Retroalimentación',
            'fecha_completado' => 'Fecha Completado',
        ];

        return ['rows' => $rows, 'headings' => $headings];
    }

    public function render()
    {
        $query = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->whereNotNull('ce.calificacion')
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('c.nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('u.name', 'like', '%'.$this->search.'%')
                    ->orWhere('u.apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('e.codigo', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->cursoFiltro) {
            $query->where('ce.curso_id', $this->cursoFiltro);
        }

        if ($this->estadoFiltro) {
            $query->where('ce.estado', $this->estadoFiltro);
        }

        if ($this->rangoNota) {
            match ($this->rangoNota) {
                'excelente' => $query->whereBetween('ce.calificacion', [9, 10]),
                'bueno' => $query->whereBetween('ce.calificacion', [7, 8.9]),
                'regular' => $query->whereBetween('ce.calificacion', [5, 6.9]),
                'insuficiente' => $query->where('ce.calificacion', '<', 5),
                default => null,
            };
        }

        $calificaciones = $query
            ->select([
                'ce.curso_id',
                'ce.estudiante_id',
                'c.nombre as curso_nombre',
                'e.codigo as estudiante_codigo',
                'u.name',
                'u.apellido',
                'ce.estado',
                'ce.calificacion',
                'ce.comentario_calificacion',
                'ce.fecha_completado',
            ])
            ->orderByDesc('ce.updated_at')
            ->paginate(15);

        // Estadísticas rápidas
        $stats = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->whereNotNull('ce.calificacion')
            ->whereNull('c.deleted_at')
            ->selectRaw('
                COUNT(*) as total_calificados,
                COALESCE(AVG(ce.calificacion), 0) as promedio,
                SUM(CASE WHEN ce.calificacion >= 9 THEN 1 ELSE 0 END) as excelentes,
                SUM(CASE WHEN ce.calificacion < 5 THEN 1 ELSE 0 END) as insuficientes
            ')
            ->first();

        return view('livewire.admin.calificar-estudiantes', [
            'calificaciones' => $calificaciones,
            'stats' => $stats,
        ])->layout('layouts.app');
    }
}
