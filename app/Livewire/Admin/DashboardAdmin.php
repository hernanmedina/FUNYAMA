<?php

namespace App\Livewire\Admin;

use App\Actions\AprobarInscripcionAction;
use App\Actions\GenerarCodigoEstudianteAction;
use App\Actions\RechazarInscripcionAction;
use App\Models\Curso;
use App\Models\Solicitud;
use App\Services\DashboardEstadisticasService;
use App\Services\ReporteExportService;
use App\Services\Utf8Sanitizer;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

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

    private DashboardEstadisticasService $estadisticasService;

    private ReporteExportService $exportService;

    private Utf8Sanitizer $sanitizer;

    private AprobarInscripcionAction $aprobarInscripcion;

    private RechazarInscripcionAction $rechazarInscripcion;

    private GenerarCodigoEstudianteAction $generarCodigo;

    public function boot()
    {
        $this->estadisticasService = app(DashboardEstadisticasService::class);
        $this->exportService = app(ReporteExportService::class);
        $this->sanitizer = app(Utf8Sanitizer::class);
        $this->aprobarInscripcion = app(AprobarInscripcionAction::class);
        $this->rechazarInscripcion = app(RechazarInscripcionAction::class);
        $this->generarCodigo = app(GenerarCodigoEstudianteAction::class);
    }

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
        $this->estadisticas = $this->estadisticasService->obtenerEstadisticas((int) $this->rangoFechas);
    }

    private function cargarDatosRecientes()
    {
        $datos = $this->estadisticasService->obtenerDatosRecientes();

        $this->cursosRecientes = $datos['cursos'];
        $this->solicitudesPendientes = $datos['solicitudes'];
        $this->estudiantesRecientes = $datos['estudiantes'];
    }

    /**
     * Hook del ciclo de vida de Livewire: se ejecuta antes de serializar la respuesta.
     * Sanitiza recursivamente TODAS las propiedades públicas para garantizar
     * que ningún byte UTF-8 inválido llegue al payload JSON.
     */
    public function dehydrate(): void
    {
        $this->estadisticas = $this->sanitizer->sanitizarValorRecursivo($this->estadisticas);
        $this->cursosRecientes = $this->sanitizer->sanitizarValorRecursivo($this->cursosRecientes);
        $this->solicitudesPendientes = $this->sanitizer->sanitizarValorRecursivo($this->solicitudesPendientes);
        $this->estudiantesRecientes = $this->sanitizer->sanitizarValorRecursivo($this->estudiantesRecientes);
        $this->cursosParaExport = $this->sanitizer->sanitizarArrayUtf8($this->cursosParaExport);
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
            $this->codigo_generado = $this->generarCodigo->execute();
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

        $resultado = $this->exportService->exportar(
            $this->tipoReporte,
            $this->subtipoReporte,
            $this->formatoReporte,
            $this->cursoFiltro
        );

        if ($resultado === null) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay registros para exportar con los filtros seleccionados.');

            return;
        }

        return $resultado;
    }

    private function cargarCursosParaExport(): void
    {
        $this->cursosParaExport = $this->exportService->obtenerCursosParaExport();
    }

    public function cerrarModal()
    {
        $this->mostrarModalResolucion = false;
        $this->solicitudActual = null;
        $this->reset(['respuesta', 'codigo_generado', 'decision']);
    }

    public function aceptarInscripcion()
    {
        $this->validate([
            'respuesta' => 'nullable|string|max:1000',
        ]);

        if (! $this->solicitudActual) {
            $this->dispatch('show-toast', type: 'error', message: 'La solicitud no se encontró.');
            $this->cerrarModal();

            return;
        }

        $resultado = $this->aprobarInscripcion->execute(
            $this->solicitudActual,
            $this->codigo_generado,
            $this->respuesta
        );

        if (! $resultado['success']) {
            $this->dispatch('show-toast', type: 'error', message: $resultado['message']);

            return;
        }

        $this->dispatch('show-toast', type: 'success', message: $resultado['message']);
        $this->cerrarModal();
        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();
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

        $this->rechazarInscripcion->execute($this->solicitudActual, $this->respuesta);

        $this->dispatch('show-toast', type: 'info', message: 'Solicitud rechazada correctamente.');
        $this->cerrarModal();
        $this->cargarEstadisticas();
        $this->cargarDatosRecientes();
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin')
            ->layout('layouts.app');
    }
}
