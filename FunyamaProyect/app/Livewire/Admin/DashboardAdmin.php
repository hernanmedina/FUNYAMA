<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\Solicitud;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
    public ?Solicitud $solicitudActual = null;
    public string $respuesta = '';
    public string $decision = ''; // 'aceptar' o 'rechazar'

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
        try {
            $this->estadisticas['cursos_populares'] = Curso::withCount('estudiantes')
                ->orderBy('estudiantes_count', 'desc')
                ->take(5)
                ->get();
        } catch (\Exception $e) {
            // Fallback si hay error con la estructura de BD
            $this->estadisticas['cursos_populares'] = Curso::take(5)->get();
        }
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

    public function togglePublicacion($cursoCodigo)
    {
        $curso = Curso::where('codigo', $cursoCodigo)->firstOrFail();
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
        
        // Si es una solicitud de inscripción, abrir el modal
        if ($solicitud->tipo === 'inscripcion') {
            $this->solicitudActual = $solicitud;
            $this->mostrarModalResolucion = true;
            $this->reset(['respuesta', 'decision']);
            return;
        }

        // Para otros tipos de solicitud, marcar como resuelta directamente
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

    public function cerrarModal()
    {
        $this->mostrarModalResolucion = false;
        $this->solicitudActual = null;
        $this->reset(['respuesta', 'decision']);
    }

    public function aceptarInscripcion()
    {
        Log::info('Iniciando aceptarInscripcion', [
            'solicitud_actual' => $this->solicitudActual?->idSolicitud,
            'respuesta_length' => strlen($this->respuesta)
        ]);

        // Validación usando el sistema de Livewire
        $this->validate([
            'respuesta' => 'required|min:10'
        ], [
            'respuesta.required' => 'La respuesta es obligatoria.',
            'respuesta.min' => 'La respuesta debe tener al menos 10 caracteres.'
        ]);

        if (!$this->solicitudActual) {
            Log::error('solicitudActual es null en aceptarInscripcion');
            session()->flash('flash.banner', 'La solicitud no se encontró.');
            session()->flash('flash.bannerStyle', 'danger');
            $this->cerrarModal();
            return;
        }

        try {
            $datos = $this->solicitudActual->datos_adicionales;
            $codigoCurso = $datos['codigo_curso'] ?? null;
            $estudianteCodigo = $datos['estudiante_codigo'] ?? null;

            Log::info('Datos extraídos de solicitud', [
                'codigo_curso' => $codigoCurso,
                'estudiante_codigo' => $estudianteCodigo,
            ]);

            if (!$codigoCurso || !$estudianteCodigo) {
                throw new \Exception('Datos de inscripción incompletos. Falta código de curso o estudiante.');
            }

            $curso = Curso::where('codigo', $codigoCurso)->first();
            if (!$curso) {
                throw new \Exception("El curso con código '{$codigoCurso}' no existe.");
            }

            $estudiante = Estudiante::where('codigo', $estudianteCodigo)->first();
            if (!$estudiante) {
                throw new \Exception("El estudiante con código '{$estudianteCodigo}' no existe.");
            }

            // Verificar si ya está inscrito
            if ($estudiante->cursos()->where('codigo', $codigoCurso)->exists()) {
                throw new \Exception('El estudiante ya está inscrito en este curso.');
            }

            // Verificar cupo disponible
            if ($curso->cupo_disponible <= 0) {
                throw new \Exception('No hay cupos disponibles en este curso.');
            }

            Log::info('Validaciones pasadas, procediendo con inscripción', [
                'estudiante_codigo' => $estudianteCodigo,
                'curso_codigo' => $codigoCurso,
                'precio' => $curso->precioFinal
            ]);

            // Inscribir al estudiante
            $estudiante->cursos()->attach($codigoCurso, [
                'estado' => 'en_progreso',
                'fecha_inscripcion' => now(),
                'pago_realizado' => $curso->precioFinal,
                'estado_pago' => 'completo',
                'progreso' => 0
            ]);

            Log::info('Estudiante inscrito en el curso');

            // Decrementar cupo disponible
            $curso->decrement('cupo_disponible');

            Log::info('Cupo decrementado');

            // Obtener ID del administrador
            $adminId = null;
            $admin = auth()->user()?->administrador;
            if ($admin) {
                $adminId = $admin->idAdmin;
            }

            Log::info('Admin ID: ' . ($adminId ?? 'null'));

            // Actualizar solicitud
            $this->solicitudActual->update([
                'estado' => 'resuelta',
                'respuesta' => $this->respuesta,
                'fecha_respuesta' => now(),
                'atendido_por_admin' => $adminId
            ]);

            Log::info('Solicitud actualizada a resuelta');

            session()->flash('flash.banner', '✓ Inscripción aceptada y estudiante inscrito en el curso.');
            session()->flash('flash.bannerStyle', 'success');
            $this->cerrarModal();
            $this->cargarEstadisticas();
            $this->cargarDatosRecientes();

        } catch (\Exception $e) {
            Log::error('Error en aceptarInscripcion: ' . $e->getMessage(), [
                'solicitud_id' => $this->solicitudActual?->idSolicitud,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('flash.banner', 'Error: ' . $e->getMessage());
            session()->flash('flash.bannerStyle', 'danger');
        }
    }

    public function rechazarInscripcion()
    {
        Log::info('Iniciando rechazarInscripcion', [
            'solicitud_actual' => $this->solicitudActual?->idSolicitud,
            'respuesta_length' => strlen($this->respuesta)
        ]);

        // Validación usando el sistema de Livewire
        $this->validate([
            'respuesta' => 'required|min:10'
        ], [
            'respuesta.required' => 'La respuesta es obligatoria.',
            'respuesta.min' => 'La respuesta debe tener al menos 10 caracteres.'
        ]);

        if (!$this->solicitudActual) {
            Log::error('solicitudActual es null en rechazarInscripcion');
            session()->flash('flash.banner', 'La solicitud no se encontró.');
            session()->flash('flash.bannerStyle', 'danger');
            $this->cerrarModal();
            return;
        }

        try {
            // Obtener ID del administrador
            $adminId = null;
            $admin = auth()->user()?->administrador;
            if ($admin) {
                $adminId = $admin->idAdmin;
            }

            $this->solicitudActual->update([
                'estado' => 'cancelada',
                'respuesta' => $this->respuesta,
                'fecha_respuesta' => now(),
                'atendido_por_admin' => $adminId
            ]);

            Log::info('Inscripción rechazada exitosamente', [
                'solicitud_id' => $this->solicitudActual->idSolicitud,
                'admin_id' => $adminId
            ]);

            session()->flash('flash.banner', '✗ Solicitud de inscripción rechazada.');
            session()->flash('flash.bannerStyle', 'success');
            $this->cerrarModal();
            $this->cargarEstadisticas();
            $this->cargarDatosRecientes();

        } catch (\Exception $e) {
            Log::error('Error en rechazarInscripcion: ' . $e->getMessage(), [
                'solicitud_id' => $this->solicitudActual?->idSolicitud,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            session()->flash('flash.banner', 'Error: ' . $e->getMessage());
            session()->flash('flash.bannerStyle', 'danger');
        }
    }

    public function render()
    {
        return view('livewire.admin.dashboard-admin')
            ->layout('layouts.app');
    }
}
