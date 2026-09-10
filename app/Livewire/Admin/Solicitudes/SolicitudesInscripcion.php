<?php

namespace App\Livewire\Admin\Solicitudes;

use App\Actions\AprobarInscripcionAction;
use App\Actions\GenerarCodigoEstudianteAction;
use App\Actions\RechazarInscripcionAction;
use App\Models\InscripcionEvento;
use App\Models\Solicitud;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitudesInscripcion extends Component
{
    use WithPagination;

    private AprobarInscripcionAction $aprobarInscripcion;

    private RechazarInscripcionAction $rechazarInscripcion;

    private GenerarCodigoEstudianteAction $generarCodigo;

    public function boot()
    {
        $this->aprobarInscripcion = app(AprobarInscripcionAction::class);
        $this->rechazarInscripcion = app(RechazarInscripcionAction::class);
        $this->generarCodigo = app(GenerarCodigoEstudianteAction::class);
    }

    public $search = '';

    public $perPage = 15;

    public $filtroEstado = 'pendiente';

    // Modal de revisión
    public $mostrarModalRevision = false;

    public $solicitudId = null;

    public $solicitudSeleccionada = null;

    public $respuesta = '';

    public $codigo_generado = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filtroEstado' => ['except' => 'pendiente'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedFiltroEstado()
    {
        $this->resetPage();
    }

    public function getSolicitudesProperty()
    {
        $solicitudes = Solicitud::where('tipo', 'inscripcion')
            ->when($this->filtroEstado !== 'todas', function ($query) {
                $query->where('estado', $this->filtroEstado);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('asunto', 'like', '%'.$this->search.'%')
                        ->orWhere('email_contacto', 'like', '%'.$this->search.'%')
                        ->orWhere('mensaje', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (Solicitud $solicitud) => $this->mapearSolicitudCurso($solicitud));

        $inscripcionesEventos = InscripcionEvento::with('evento')
            ->when($this->filtroEstado !== 'todas', function ($query) {
                $query->where('estado', $this->filtroEstado === 'resuelta' ? 'confirmada' : $this->filtroEstado);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%'.$this->search.'%')
                        ->orWhere('apellido', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhereHas('evento', function ($eventoQuery) {
                            $eventoQuery->where('titulo', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn (InscripcionEvento $inscripcion) => $this->mapearInscripcionEvento($inscripcion));

        return $solicitudes
            ->concat($inscripcionesEventos)
            ->sortByDesc('created_at')
            ->values();
    }

    /**
     * Normaliza una solicitud de curso al formato unificado de la tabla.
     *
     * @return array<string, mixed>
     */
    private function mapearSolicitudCurso(Solicitud $solicitud): array
    {
        return [
            'id' => $solicitud->idSolicitud,
            'tipo' => 'curso',
            'nombre' => $solicitud->datos_adicionales['nombre'] ?? '',
            'apellido' => $solicitud->datos_adicionales['apellido'] ?? '',
            'email' => $solicitud->email_contacto,
            'detalle' => $solicitud->datos_adicionales['nombre_curso'] ?? 'N/A',
            'estado' => $solicitud->estado,
            'created_at' => $solicitud->created_at,
            'modelo' => $solicitud,
        ];
    }

    /**
     * Normaliza una inscripción a evento al formato unificado de la tabla.
     *
     * @return array<string, mixed>
     */
    private function mapearInscripcionEvento(InscripcionEvento $inscripcion): array
    {
        return [
            'id' => $inscripcion->idInscripcion,
            'tipo' => 'evento',
            'nombre' => $inscripcion->nombre,
            'apellido' => $inscripcion->apellido,
            'email' => $inscripcion->email,
            'detalle' => $inscripcion->evento?->titulo ?? 'Evento eliminado',
            'estado' => $inscripcion->estado === 'confirmada' ? 'resuelta' : $inscripcion->estado,
            'created_at' => $inscripcion->created_at,
            'modelo' => $inscripcion,
        ];
    }

    public function abrirModalRevision($solicitudId)
    {
        $this->solicitudId = $solicitudId;
        $this->solicitudSeleccionada = Solicitud::findOrFail($solicitudId);
        $this->respuesta = '';
        $this->codigo_generado = $this->generarCodigo->execute();
        $this->mostrarModalRevision = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModalRevision = false;
        $this->solicitudId = null;
        $this->solicitudSeleccionada = null;
        $this->respuesta = '';
        $this->codigo_generado = '';
    }

    /**
     * Aprobar la solicitud: crea el usuario, el estudiante y lo inscribe en el curso.
     */
    public function aprobarSolicitud()
    {
        $this->validate([
            'respuesta' => 'nullable|string|max:1000',
            'codigo_generado' => 'required|string|max:100|unique:estudiantes,codigo',
        ]);

        $solicitud = Solicitud::findOrFail($this->solicitudId);

        $this->authorize('update', $solicitud);

        $resultado = $this->aprobarInscripcion->execute(
            $solicitud,
            $this->codigo_generado,
            $this->respuesta
        );

        if (! $resultado['success']) {
            $this->addError('general', $resultado['message']);

            return;
        }

        $this->dispatch('show-toast', type: 'success', message: $resultado['message']);
        $this->cerrarModal();
    }

    /**
     * Rechazar la solicitud.
     */
    public function rechazarSolicitud()
    {
        $this->validate([
            'respuesta' => 'required|string|min:10|max:1000',
        ]);

        $solicitud = Solicitud::findOrFail($this->solicitudId);

        $this->authorize('update', $solicitud);

        $this->rechazarInscripcion->execute($solicitud, $this->respuesta);

        $this->dispatch('show-toast', type: 'info', message: 'Solicitud rechazada correctamente.');
        $this->cerrarModal();
    }

    /**
     * Marcar como en proceso.
     */
    public function marcarEnProceso($solicitudId)
    {
        $solicitud = Solicitud::findOrFail($solicitudId);

        $this->authorize('update', $solicitud);

        $solicitud->update(['estado' => 'en_proceso']);

        $this->dispatch('show-toast', type: 'info', message: 'Solicitud marcada como "En proceso".');
    }

    public function render()
    {
        return view('livewire.admin.solicitudes.solicitudes-inscripcion')
            ->layout('layouts.app');
    }
}
