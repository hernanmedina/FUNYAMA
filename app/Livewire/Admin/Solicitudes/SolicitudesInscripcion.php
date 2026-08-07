<?php

namespace App\Livewire\Admin\Solicitudes;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use App\Notifications\CredencialesEstudiante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class SolicitudesInscripcion extends Component
{
    use WithPagination;

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
        return Solicitud::where('tipo', 'inscripcion')
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
            ->paginate($this->perPage);
    }

    public function abrirModalRevision($solicitudId)
    {
        $this->solicitudId = $solicitudId;
        $this->solicitudSeleccionada = Solicitud::findOrFail($solicitudId);
        $this->respuesta = '';
        $this->codigo_generado = $this->generarCodigoEstudiante();
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
        $datos = $solicitud->datos_adicionales;

        // Verificar que el curso exista y tenga cupos
        $curso = Curso::where('codigo', $datos['codigo_curso'])->first();
        if (! $curso) {
            $this->addError('general', 'El curso asociado a esta solicitud ya no existe.');

            return;
        }

        if ($curso->cupo_disponible <= 0) {
            $this->addError('general', 'El curso "'.$curso->nombre.'" ya no tiene cupos disponibles.');

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
                // Si ya existe, solo actualizamos algunos datos y mantenemos su password
                // Pero generamos una nueva contraseña para enviarle
                $user->update([
                    'name' => $datos['nombre'] ?? $user->name,
                    'apellido' => $datos['apellido'] ?? $user->apellido,
                    'telefono' => $solicitud->telefono ?? $user->telefono,
                ]);
            }

            // 3. Verificar si ya existe un estudiante asociado a ese usuario
            $estudiante = Estudiante::where('user_id', $user->id)->first();

            if (! $estudiante) {
                // Crear el estudiante
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
                $precioInscripcion = (float) $curso->precioFinal;

                $estudiante->cursos()->attach($curso->codigo, [
                    'estado' => 'inscrito',
                    'pago_realizado' => $precioInscripcion,
                    'estado_pago' => $precioInscripcion > 0 ? 'completo' : 'pendiente',
                    'fecha_inscripcion' => now(),
                    'progreso' => 0,
                ]);

                // Actualizar cupo disponible
                $curso->decrement('cupo_disponible');
            }

            // 5. Marcar solicitud como resuelta
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
                \Log::error('Error al enviar credenciales: '.$e->getMessage());
            }

            $this->dispatch('show-toast', type: 'success', message: 'Solicitud aprobada. Estudiante creado e inscrito exitosamente. Se han enviado las credenciales por correo.');
            $this->cerrarModal();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Error al procesar la solicitud: '.$e->getMessage());
        }
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
        $admin = auth()->user()->administrador;

        $solicitud->marcarComoResuelta($this->respuesta, $admin?->idAdmin);
        // Usamos estado 'cancelada' para rechazos
        $solicitud->update(['estado' => 'cancelada']);

        $this->dispatch('show-toast', type: 'info', message: 'Solicitud rechazada correctamente.');
        $this->cerrarModal();
    }

    /**
     * Marcar como en proceso.
     */
    public function marcarEnProceso($solicitudId)
    {
        $solicitud = Solicitud::findOrFail($solicitudId);
        $solicitud->update(['estado' => 'en_proceso']);

        $this->dispatch('show-toast', type: 'info', message: 'Solicitud marcada como "En proceso".');
    }

    /**
     * Generar un código único de estudiante.
     */
    private function generarCodigoEstudiante()
    {
        $year = now()->year;
        $month = now()->format('m');
        $random = strtoupper(Str::random(4));

        $codigo = "EST-{$year}{$month}-{$random}";

        // Verificar que no exista
        while (Estudiante::where('codigo', $codigo)->exists()) {
            $random = strtoupper(Str::random(4));
            $codigo = "EST-{$year}{$month}-{$random}";
        }

        return $codigo;
    }

    public function render()
    {
        return view('livewire.admin.solicitudes.solicitudes-inscripcion')
            ->layout('layouts.app');
    }
}
