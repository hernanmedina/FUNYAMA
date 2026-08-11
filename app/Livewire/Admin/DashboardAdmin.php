<?php

namespace App\Livewire\Admin;

use App\Exports\ReporteDashboardExport;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Evento;
use App\Models\Solicitud;
use App\Models\User;
use App\Notifications\CredencialesEstudiante;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardAdmin extends Component
{
    use WithPagination;

    public $estadisticas;

    public $cursosRecientes;

    public $solicitudesPendientes;

    public $estudiantesRecientes;

    // Filtros para estadísticas
    public $rangoFechas = '30'; // 7, 30, 90, 365 días

    // Modal para resolver solicitud de inscripción
    public bool $mostrarModalResolucion = false;

    public bool $mostrarModalExport = false;

    public string $tipoReporte = 'cursos';

    public string $subtipoReporte = 'gratuitos';

    public string $formatoReporte = 'excel';

    public ?string $cursoFiltro = null;

    public array $cursosParaExport = [];

    public ?Solicitud $solicitudActual = null;

    public string $respuesta = '';

    public string $codigo_generado = '';

    public string $decision = ''; // 'aceptar' o 'rechazar'

    protected $queryString = [
        'rangoFechas' => ['except' => '30'],
    ];

    public function mount()
    {
        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();
        $this->cargarCursosParaExport();
    }

    public function updatedRangoFechas()
    {
        $this->cargarEstadisticas();
    }

    public function actualizarEstadoPago(string $cursoId, string $estudianteId, string $estadoPago): void
    {
        $estadoPago = in_array($estadoPago, ['pendiente', 'parcial', 'completo'], true)
            ? $estadoPago
            : 'pendiente';

        $curso = Curso::find($cursoId);
        if (! $curso) {
            $this->dispatch('show-toast', type: 'error', message: 'No se encontró el curso asociado a la matrícula.');

            return;
        }

        $payload = [
            'estado_pago' => $estadoPago,
        ];

        if ($estadoPago === 'pendiente') {
            $payload['pago_realizado'] = 0;
        }

        if ($estadoPago === 'completo') {
            $payload['pago_realizado'] = (float) $curso->precioFinal;
        }

        DB::table('curso_estudiante')
            ->where('curso_id', $cursoId)
            ->where('estudiante_id', $estudianteId)
            ->update($payload);

        $this->cargarEstadisticas();

        $this->dispatch('show-toast', type: 'success', message: 'Estado de pago actualizado correctamente.');
    }

    private function cargarEstadisticas()
    {
        $fechaInicio = now()->subDays($this->rangoFechas);
        $solicitudesInscripcion = Solicitud::query()->where('tipo', 'inscripcion');

        $this->estadisticas = [
            'total_cursos' => Curso::count(),
            'cursos_publicados' => Curso::where('publicado', true)->count(),
            'total_eventos_publicados' => Evento::where('publicado', true)->count(),
            'total_estudiantes' => Estudiante::where('activo', true)->count(),
            'total_usuarios' => User::count(),
            'solicitudes_pendientes' => (clone $solicitudesInscripcion)->where('estado', 'pendiente')->count(),
            'ingresos_totales' => (float) $this->obtenerIngresosEstadisticos(),
            'matriculas_gratuitas' => (int) DB::table('curso_estudiante')
                ->where('estado_pago', 'pendiente')
                ->where('pago_realizado', 0)
                ->count(),

            // Estadísticas del período
            'nuevos_estudiantes' => Estudiante::where('created_at', '>=', $fechaInicio)->count(),
            'nuevos_cursos' => Curso::where('created_at', '>=', $fechaInicio)->count(),
            'ingresos_recientes' => (float) $this->obtenerIngresosEstadisticos($fechaInicio),

            'solicitudes_resueltas' => (clone $solicitudesInscripcion)
                ->where('estado', 'resuelta')
                ->where('updated_at', '>=', $fechaInicio)
                ->count(),
        ];

        // Cursos más populares (con más estudiantes)
        try {
            $this->estadisticas['cursos_populares'] = $this->normalizarColeccionModelos(Curso::withCount('estudiantes')
                ->orderBy('estudiantes_count', 'desc')
                ->take(5)
                ->get());
        } catch (\Exception $e) {
            // Fallback si hay error con la estructura de BD
            $this->estadisticas['cursos_populares'] = $this->normalizarColeccionModelos(Curso::take(5)->get());
        }
    }

    private function obtenerIngresosEstadisticos(?Carbon $fechaInicio = null): float
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

    private function cargarDatosRecientes()
    {
        $this->cursosRecientes = $this->normalizarColeccionModelos(Curso::with('administrador.user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get());

        $this->solicitudesPendientes = $this->normalizarColeccionModelos(Solicitud::with('usuario')
            ->where('tipo', 'inscripcion')
            ->where('estado', 'pendiente')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get());

        $this->estudiantesRecientes = $this->normalizarColeccionModelos(Estudiante::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get());
    }

    private function normalizarColeccionModelos($coleccion)
    {
        if (! $coleccion instanceof Collection) {
            return $coleccion;
        }

        $coleccion->each(function ($modelo) {
            $this->normalizarModeloUtf8($modelo);
        });

        return $coleccion;
    }

    private function normalizarModeloUtf8($modelo): void
    {
        if (! is_object($modelo)) {
            return;
        }

        foreach ($modelo->getAttributes() as $campo => $valor) {
            if (is_string($valor)) {
                $modelo->setAttribute($campo, $this->sanitizarTextoUtf8($valor));
            }
        }

        foreach ($modelo->getRelations() as $relacion => $valor) {
            if ($valor instanceof Collection) {
                $valor->each(function ($item) {
                    $this->normalizarModeloUtf8($item);
                });

                continue;
            }

            if ($valor instanceof Model) {
                $this->normalizarModeloUtf8($valor);
            }
        }
    }

    private function sanitizarTextoUtf8(string $valor): string
    {
        // Paso 1: Eliminar secuencias de bytes UTF-8 inválidas usando mb_convert_encoding.
        // PHP 8.0+ reemplaza caracteres inválidos en lugar de retornar false.
        $texto = @mb_convert_encoding($valor, 'UTF-8', 'UTF-8');

        if ($texto === false) {
            // Fallback con iconv en caso extremo
            $texto = @iconv('UTF-8', 'UTF-8//IGNORE', $valor) ?: '';
        }

        if ($texto === '') {
            return '';
        }

        // Paso 2: Eliminar caracteres de control (excepto tab, newline, carriage return)
        // Se usa un patrón sin modificador /u para evitar errores de PCRE en edge cases.
        $resultado = @preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $texto);

        return is_string($resultado) ? $resultado : $texto;
    }

    /**
     * Sanitiza recursivamente todos los valores string dentro de un array (incluyendo sub-arrays).
     */
    private function sanitizarArrayUtf8(array $datos): array
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return $this->sanitizarTextoUtf8($item);
            }

            if (is_array($item)) {
                return $this->sanitizarArrayUtf8($item);
            }

            return $item;
        }, $datos);
    }

    /**
     * Hook del ciclo de vida de Livewire: se ejecuta antes de serializar la respuesta.
     * Sanitiza recursivamente TODAS las propiedades públicas para garantizar
     * que ningún byte UTF-8 inválido llegue al payload JSON.
     */
    public function dehydrate(): void
    {
        $this->estadisticas = $this->sanitizarValorRecursivo($this->estadisticas);
        $this->cursosRecientes = $this->sanitizarValorRecursivo($this->cursosRecientes);
        $this->solicitudesPendientes = $this->sanitizarValorRecursivo($this->solicitudesPendientes);
        $this->estudiantesRecientes = $this->sanitizarValorRecursivo($this->estudiantesRecientes);
        $this->cursosParaExport = $this->sanitizarArrayUtf8($this->cursosParaExport);
    }

    /**
     * Sanitiza recursivamente cualquier estructura de datos (string, array, Collection,
     * Eloquent Model, stdClass) eliminando bytes UTF-8 inválidos y caracteres de control.
     *
     * @param  mixed  $valor
     * @return mixed
     */
    private function sanitizarValorRecursivo($valor)
    {
        if (is_string($valor)) {
            return $this->sanitizarTextoUtf8($valor);
        }

        if (is_array($valor)) {
            return array_map(fn ($item) => $this->sanitizarValorRecursivo($item), $valor);
        }

        if ($valor instanceof Collection) {
            return $valor->map(fn ($item) => $this->sanitizarValorRecursivo($item));
        }

        if ($valor instanceof Model) {
            foreach ($valor->getAttributes() as $campo => $v) {
                if (is_string($v)) {
                    $valor->setAttribute($campo, $this->sanitizarTextoUtf8($v));
                }
            }

            foreach ($valor->getRelations() as $nombreRelacion => $relacionado) {
                $valor->setRelation($nombreRelacion, $this->sanitizarValorRecursivo($relacionado));
            }

            return $valor;
        }

        if ($valor instanceof \stdClass) {
            foreach ($valor as $prop => $v) {
                if (is_string($v)) {
                    $valor->{$prop} = $this->sanitizarTextoUtf8($v);
                } elseif (is_array($v) || is_object($v)) {
                    $valor->{$prop} = $this->sanitizarValorRecursivo($v);
                }
            }

            return $valor;
        }

        return $valor;
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

    public function togglePublicacion($cursoCodigo)
    {
        $curso = Curso::where('codigo', $cursoCodigo)->firstOrFail();
        $curso->update(['publicado' => ! $curso->publicado]);

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

        // Solo las solicitudes de inscripción abren el modal de resolución.
        if ($solicitud->tipo === 'inscripcion') {
            $this->solicitudActual = $solicitud;
            $this->codigo_generado = $this->generarCodigoEstudiante();
            $this->mostrarModalResolucion = true;
            $this->reset(['respuesta', 'decision']);

            return;
        }

        // Para otros tipos de solicitud, marcar como resuelta directamente
        $solicitud->update([
            'estado' => 'resuelta',
            'fecha_respuesta' => now(),
            'atendido_por_admin' => auth()->user()?->administrador?->idAdmin,
        ]);

        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Solicitud marcada como resuelta.'
        );
    }

    public function abrirModalExport(): void
    {
        $this->cargarCursosParaExport();
        $this->mostrarModalExport = true;
    }

    public function cerrarModalExport(): void
    {
        $this->mostrarModalExport = false;
    }

    public function updatedTipoReporte(): void
    {
        $this->cursoFiltro = null;

        $this->subtipoReporte = match ($this->tipoReporte) {
            'cursos' => 'gratuitos',
            'estudiantes' => 'total',
            'eventos' => 'publicados',
        };
    }

    public function exportarReporte()
    {
        $this->validate([
            'tipoReporte' => ['required', 'in:cursos,estudiantes,eventos'],
            'subtipoReporte' => ['required', 'string'],
            'formatoReporte' => ['required', 'in:excel,csv'],
            'cursoFiltro' => ['nullable', 'string'],
        ]);

        $rows = $this->obtenerDatosExportables();
        $headers = $this->obtenerCabecerasExport();

        if ($rows === []) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay registros para exportar con los filtros seleccionados.');

            return;
        }

        if ($this->formatoReporte === 'csv') {
            return $this->descargarCsv($rows, $headers);
        }

        return Excel::download(
            new ReporteDashboardExport($rows, $headers),
            sprintf('reporte-%s-%s.xlsx', $this->tipoReporte, $this->subtipoReporte)
        );
    }

    private function descargarCsv(array $rows, array $headers): StreamedResponse
    {
        $nombreArchivo = sprintf('reporte-%s-%s.csv', $this->tipoReporte, $this->subtipoReporte);
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

    private function cargarCursosParaExport(): void
    {
        $this->cursosParaExport = Curso::query()
            ->select(['codigo', 'nombre'])
            ->orderBy('nombre')
            ->get()
            ->map(fn ($curso) => [
                'codigo' => $curso->codigo,
                'nombre' => $this->sanitizarTextoUtf8($curso->nombre),
            ])
            ->toArray();
    }

    private function obtenerCabecerasExport(): array
    {
        return match ($this->tipoReporte) {
            'cursos' => match ($this->subtipoReporte) {
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
            'estudiantes' => match ($this->subtipoReporte) {
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

    private function obtenerDatosExportables(): array
    {
        return match ($this->tipoReporte) {
            'cursos' => $this->obtenerDatosCursosExportables(),
            'estudiantes' => $this->obtenerDatosEstudiantesExportables(),
            default => $this->obtenerDatosEventosExportables(),
        };
    }

    private function obtenerDatosCursosExportables(): array
    {
        $query = Curso::query()
            ->withCount('estudiantes')
            ->select(['codigo', 'nombre', 'cupo_total', 'cupo_disponible', 'precio_regular', 'precio_descuento']);

        if ($this->cursoFiltro) {
            $query->where('codigo', $this->cursoFiltro);
        }

        return $this->sanitizarArrayUtf8(match ($this->subtipoReporte) {
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

    private function obtenerDatosEstudiantesExportables(): array
    {
        return $this->sanitizarArrayUtf8(match ($this->subtipoReporte) {
            'por_curso' => DB::table('curso_estudiante as ce')
                ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
                ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
                ->join('users as u', 'u.id', '=', 'e.user_id')
                ->when($this->cursoFiltro, fn ($query) => $query->where('ce.curso_id', $this->cursoFiltro))
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
                ->when($this->cursoFiltro, fn ($query) => $query->where('ce.curso_id', $this->cursoFiltro))
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
                ->when($this->cursoFiltro, fn ($query) => $query->where('ce.curso_id', $this->cursoFiltro))
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
        return $this->sanitizarArrayUtf8(Evento::query()
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

    private function obtenerTituloReporte(): string
    {
        return match ($this->tipoReporte) {
            'cursos' => match ($this->subtipoReporte) {
                'gratuitos' => 'Reporte de cursos gratuitos',
                'con_ingresos' => 'Reporte de cursos con ingresos',
                default => 'Reporte de cursos por curso',
            },
            'estudiantes' => match ($this->subtipoReporte) {
                'por_curso' => 'Reporte de estudiantes por curso',
                'cursos_terminados' => 'Reporte de estudiantes con cursos terminados',
                'cursos_matriculados' => 'Reporte de estudiantes matriculados',
                default => 'Reporte de estudiantes',
            },
            default => 'Reporte de eventos publicados',
        };
    }

    public function cerrarModal()
    {
        $this->mostrarModalResolucion = false;
        $this->solicitudActual = null;
        $this->reset(['respuesta', 'codigo_generado', 'decision']);
    }

    public function aceptarInscripcion()
    {
        Log::info('Iniciando aceptarInscripcion (Dashboard)', [
            'solicitud_id' => $this->solicitudActual?->idSolicitud,
        ]);

        $this->validate([
            'respuesta' => 'nullable|string|max:1000',
        ]);

        if (! $this->solicitudActual) {
            $this->dispatch('show-toast', type: 'error', message: 'La solicitud no se encontró.');
            $this->cerrarModal();

            return;
        }

        $solicitud = $this->solicitudActual;
        $datos = $solicitud->datos_adicionales;

        // Verificar que el curso exista y tenga cupos
        $curso = Curso::where('codigo', $datos['codigo_curso'])->first();
        if (! $curso) {
            $this->dispatch('show-toast', type: 'error', message: 'El curso asociado a esta solicitud ya no existe.');

            return;
        }

        if ($curso->cupo_disponible <= 0) {
            $this->dispatch('show-toast', type: 'error', message: 'El curso "'.$curso->nombre.'" ya no tiene cupos disponibles.');

            return;
        }

        DB::beginTransaction();
        try {
            // 1. Usar el documento_ID como contraseña
            $password = (string) ($datos['documento_ID'] ?? Str::random(10));

            // 2. Verificar si ya existe un usuario con ese email
            $user = User::where('email', $solicitud->email_contacto)->first();

            if (! $user) {
                // Crear el usuario
                $user = User::create([
                    'name' => $datos['nombre'] ?? $solicitud->email_contacto,
                    'apellido' => $datos['apellido'] ?? '',
                    'email' => $solicitud->email_contacto,
                    'documento_ID' => $datos['documento_ID'] ?? 'S/N',
                    'password' => Hash::make($password),
                    'telefono' => $solicitud->telefono ?? '',
                    'direccion' => $datos['direccion'] ?? '',
                    'role' => 'estu',
                ]);
            } else {
                // Si ya existe, solo actualizamos algunos datos
                $user->update([
                    'name' => $datos['nombre'] ?? $user->name,
                    'apellido' => $datos['apellido'] ?? $user->apellido,
                    'telefono' => $solicitud->telefono ?? $user->telefono,
                ]);
            }

            // 3. Verificar si ya existe un estudiante asociado a ese usuario
            $estudiante = Estudiante::where('user_id', $user->id)->first();

            if (! $estudiante) {
                // Crear el estudiante con el código generado al abrir el modal
                $estudiante = Estudiante::create([
                    'codigo' => $this->codigo_generado,
                    'user_id' => $user->id,
                    'fecha_nacimiento' => ! empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
                    'genero' => ! empty($datos['genero']) ? $datos['genero'] : null,
                    'nivel_educativo' => ! empty($datos['nivel_educativo']) ? $datos['nivel_educativo'] : null,
                    'intereses' => 'Inscrito vía solicitud - Curso: '.($datos['nombre_curso'] ?? ''),
                    'fecha_registro' => now(),
                    'activo' => true,
                ]);
            }

            // 4. Inscribir al estudiante en el curso (si no está ya inscrito)
            $yaInscrito = $estudiante->cursos()->where('curso_id', $curso->codigo)->exists();

            if (! $yaInscrito) {
                $estudiante->cursos()->attach($curso->codigo, [
                    'estado' => 'inscrito',
                    'pago_realizado' => 0,
                    'estado_pago' => 'pendiente',
                    'fecha_inscripcion' => now(),
                    'progreso' => 0,
                ]);

                // Actualizar cupo disponible
                $curso->decrement('cupo_disponible');
            }

            // 5. Marcar solicitud como resuelta (usando el método del modelo)
            $admin = auth()->user()->administrador;
            $solicitud->marcarComoResuelta(
                $this->respuesta ?: 'Solicitud aprobada. Se ha creado tu cuenta y se te ha inscrito en el curso.',
                $admin?->idAdmin
            );

            DB::commit();

            // 6. Enviar correo con credenciales
            try {
                $user->notify(new CredencialesEstudiante(
                    $user->name,
                    $user->email,
                    $password,
                    $estudiante->codigo
                ));
            } catch (\Exception $e) {
                // Si falla el envío de correo, no detenemos el proceso
                Log::error('Error al enviar credenciales: '.$e->getMessage());
            }

            $this->dispatch('show-toast', type: 'success', message: 'Solicitud aprobada. Estudiante creado e inscrito exitosamente. Se han enviado las credenciales por correo.');
            $this->cerrarModal();
            $this->cargarEstadisticas();
            $this->cargarDatosRecientes();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en aceptarInscripcion (Dashboard): '.$e->getMessage(), [
                'solicitud_id' => $this->solicitudActual?->idSolicitud,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('show-toast', type: 'error', message: 'Error al procesar la solicitud: '.$e->getMessage());
        }
    }

    public function rechazarInscripcion()
    {
        $this->validate([
            'respuesta' => 'required|string|min:10|max:1000',
        ]);

        if (! $this->solicitudActual) {
            $this->dispatch('show-toast', type: 'error', message: 'La solicitud no se encontró.');
            $this->cerrarModal();

            return;
        }

        $solicitud = $this->solicitudActual;
        $admin = auth()->user()->administrador;

        $solicitud->marcarComoResuelta($this->respuesta, $admin?->idAdmin);
        // Usamos estado 'cancelada' para rechazos (mismo comportamiento que SolicitudesInscripcion)
        $solicitud->update(['estado' => 'cancelada']);

        $this->dispatch('show-toast', type: 'info', message: 'Solicitud rechazada correctamente.');
        $this->cerrarModal();
        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();
    }

    private function generarCodigoEstudiante()
    {
        $year = now()->year;
        $month = now()->format('m');
        $random = strtoupper(Str::random(4));
        $codigo = "EST-{$year}{$month}-{$random}";

        while (Estudiante::where('codigo', $codigo)->exists()) {
            $random = strtoupper(Str::random(4));
            $codigo = "EST-{$year}{$month}-{$random}";
        }

        return $codigo;
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin')
            ->layout('layouts.app');
    }
}
