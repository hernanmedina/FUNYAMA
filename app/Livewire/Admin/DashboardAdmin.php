<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\Solicitud;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Notifications\CredencialesEstudiante;

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
    public string $codigo_generado = '';
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
        $solicitudesInscripcion = Solicitud::query()->where('tipo', 'inscripcion');

        // CORREGIDO: Usar DB::raw para calcular ingresos en lugar del modelo CursoEstudiante
        $this->estadisticas = [
            'total_cursos' => Curso::count(),
            'cursos_publicados' => Curso::where('publicado', true)->count(),
            'total_estudiantes' => Estudiante::where('activo', true)->count(),
            'total_usuarios' => User::count(),
            'solicitudes_pendientes' => (clone $solicitudesInscripcion)->where('estado', 'pendiente')->count(),

            // CORREGIDO: Calcular ingresos usando DB query
            'ingresos_totales' => \DB::table('curso_estudiante')->sum('pago_realizado'),

            // Estadísticas del período
            'nuevos_estudiantes' => Estudiante::where('created_at', '>=', $fechaInicio)->count(),
            'nuevos_cursos' => Curso::where('created_at', '>=', $fechaInicio)->count(),

            // CORREGIDO: Calcular ingresos recientes usando DB query
            'ingresos_recientes' => \DB::table('curso_estudiante')
                ->where('created_at', '>=', $fechaInicio)
                ->sum('pago_realizado'),

            'solicitudes_resueltas' => (clone $solicitudesInscripcion)
                ->where('estado', 'resuelta')
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
            ->where('tipo', 'inscripcion')
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
            'atendido_por_admin' => auth()->user()?->administrador?->idAdmin
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

        if (!$this->solicitudActual) {
            $this->dispatch('show-toast', type: 'error', message: 'La solicitud no se encontró.');
            $this->cerrarModal();
            return;
        }

        $solicitud = $this->solicitudActual;
        $datos = $solicitud->datos_adicionales;

        // Verificar que el curso exista y tenga cupos
        $curso = Curso::where('codigo', $datos['codigo_curso'])->first();
        if (!$curso) {
            $this->dispatch('show-toast', type: 'error', message: 'El curso asociado a esta solicitud ya no existe.');
            return;
        }

        if ($curso->cupo_disponible <= 0) {
            $this->dispatch('show-toast', type: 'error', message: 'El curso "' . $curso->nombre . '" ya no tiene cupos disponibles.');
            return;
        }

        DB::beginTransaction();
        try {
            // 1. Usar el documento_ID como contraseña
            $password = (string) ($datos['documento_ID'] ?? Str::random(10));

            // 2. Verificar si ya existe un usuario con ese email
            $user = User::where('email', $solicitud->email_contacto)->first();

            if (!$user) {
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

            if (!$estudiante) {
                // Crear el estudiante con el código generado al abrir el modal
                $estudiante = Estudiante::create([
                    'codigo' => $this->codigo_generado,
                    'user_id' => $user->id,
                    'fecha_nacimiento' => !empty($datos['fecha_nacimiento']) ? $datos['fecha_nacimiento'] : null,
                    'genero' => !empty($datos['genero']) ? $datos['genero'] : null,
                    'nivel_educativo' => !empty($datos['nivel_educativo']) ? $datos['nivel_educativo'] : null,
                    'intereses' => 'Inscrito vía solicitud - Curso: ' . ($datos['nombre_curso'] ?? ''),
                    'fecha_registro' => now(),
                    'activo' => true,
                ]);
            }

            // 4. Inscribir al estudiante en el curso (si no está ya inscrito)
            $yaInscrito = $estudiante->cursos()->where('curso_id', $curso->codigo)->exists();

            if (!$yaInscrito) {
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
                Log::error('Error al enviar credenciales: ' . $e->getMessage());
            }

            $this->dispatch('show-toast', type: 'success', message: 'Solicitud aprobada. Estudiante creado e inscrito exitosamente. Se han enviado las credenciales por correo.');
            $this->cerrarModal();
            $this->cargarEstadisticas();
            $this->cargarDatosRecientes();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error en aceptarInscripcion (Dashboard): ' . $e->getMessage(), [
                'solicitud_id' => $this->solicitudActual?->idSolicitud,
                'trace' => $e->getTraceAsString(),
            ]);
            $this->dispatch('show-toast', type: 'error', message: 'Error al procesar la solicitud: ' . $e->getMessage());
        }
    }

    public function rechazarInscripcion()
    {
        $this->validate([
            'respuesta' => 'required|string|min:10|max:1000',
        ]);

        if (!$this->solicitudActual) {
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
