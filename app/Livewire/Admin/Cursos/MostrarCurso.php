<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Curso;
use App\Models\Solicitud;

#[Layout('layouts.cursos')]
class MostrarCurso extends Component
{
    public Curso $curso;
    public array $temarioProgreso = [];
    public bool $estaInscrito = false;

    // Modal de solicitud de inscripción
    public bool $mostrarModalSolicitud = false;

    // Datos del solicitante
    public $nombre_solicitante = '';
    public $apellido_solicitante = '';
    public $email_solicitante = '';
    public $telefono_solicitante = '';
    public $documento_solicitante = '';
    public $direccion_solicitante = '';
    public $fecha_nacimiento_solicitante = '';
    public $genero_solicitante = '';
    public $nivel_educativo_solicitante = '';
    public string $mensaje = '';
    public string $motivacion = '';

    public function mount(Curso $curso)
    {
        $this->curso = $curso;

        if (auth()->check() && auth()->user()->estudiante) {
            $estudiante = auth()->user()->estudiante;
            $inscripcion = $this->curso->estudiantes()
                ->where('estudiante_id', $estudiante->codigo)
                ->first();

            $this->estaInscrito = $inscripcion !== null;
            $this->temarioProgreso = $this->normalizarProgreso($inscripcion?->pivot?->temario_progreso ?? []);
        }
    }

    protected function normalizarProgreso($progreso): array
    {
        if (is_string($progreso)) {
            $decodificado = json_decode($progreso, true);
            if (is_array($decodificado)) {
                return $decodificado;
            }
        }

        return is_array($progreso) ? $progreso : [];
    }

    protected function calcularProgreso(array $progreso, int $total): float
    {
        if ($total === 0) {
            return 0.0;
        }

        $completados = count(array_filter($progreso, fn ($valor) => $valor === true));

        return round(($completados / $total) * 100, 2);
    }

    protected function rules()
    {
        return [
            'nombre_solicitante' => 'required|string|max:255',
            'apellido_solicitante' => 'nullable|string|max:255',
            'email_solicitante' => 'required|email|max:255',
            'telefono_solicitante' => 'nullable|string|max:30',
            'documento_solicitante' => 'required|string|max:20',
            'direccion_solicitante' => 'nullable|string|max:500',
            'fecha_nacimiento_solicitante' => 'nullable|date',
            'genero_solicitante' => 'nullable|in:masculino,femenino,otro',
            'nivel_educativo_solicitante' => 'nullable|string|max:255',
            'mensaje' => 'required|min:10',
            'motivacion' => 'required',
        ];
    }

    protected $messages = [
        'nombre_solicitante.required' => 'El nombre es obligatorio.',
        'email_solicitante.required' => 'El email es obligatorio.',
        'email_solicitante.email' => 'Debes ingresar un email válido.',
        'documento_solicitante.required' => 'El documento de identidad es obligatorio.',
        'mensaje.required' => 'El mensaje es obligatorio.',
        'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
        'motivacion.required' => 'Debes seleccionar una motivación.',
    ];

    public function abrirModalSolicitud()
    {
        // Validar que el estudiante no esté ya inscrito
        if (auth()->check() && auth()->user()->estudiante) {
            $estudiante = auth()->user()->estudiante;
            $yaInscrito = $this->curso->estudiantes()
                ->where('estudiante_id', $estudiante->codigo)
                ->exists();

            if ($yaInscrito) {
                $this->dispatch('show-toast', type: 'warning', message: 'Ya estás inscrito en este curso. No puedes solicitar inscripción nuevamente.');
                return;
            }
        }

        $this->mostrarModalSolicitud = true;

        if (auth()->check()) {
            $user = auth()->user();
            $this->nombre_solicitante = $user->name;
            $this->apellido_solicitante = $user->apellido ?? '';
            $this->email_solicitante = $user->email;
            $this->telefono_solicitante = $user->telefono ?? '';
            $this->documento_solicitante = $user->documento_ID ?? '';
            $this->direccion_solicitante = $user->direccion ?? '';

            if ($user->estudiante) {
                $this->fecha_nacimiento_solicitante = $user->estudiante->fecha_nacimiento?->format('Y-m-d');
                $this->genero_solicitante = $user->estudiante->genero;
                $this->nivel_educativo_solicitante = $user->estudiante->nivel_educativo;
            }
        }

        $this->reset(['mensaje', 'motivacion']);
    }

    public function cerrarModal()
    {
        $this->mostrarModalSolicitud = false;
        $this->reset([
            'nombre_solicitante', 'apellido_solicitante', 'email_solicitante',
            'telefono_solicitante', 'documento_solicitante', 'direccion_solicitante',
            'fecha_nacimiento_solicitante', 'genero_solicitante', 'nivel_educativo_solicitante',
            'mensaje', 'motivacion'
        ]);
    }

    public function enviarSolicitud()
    {
        $this->validate();

        // Validar que el estudiante no esté ya inscrito
        if (auth()->check() && auth()->user()->estudiante) {
            $estudiante = auth()->user()->estudiante;
            $yaInscrito = $this->curso->estudiantes()
                ->where('estudiante_id', $estudiante->codigo)
                ->exists();

            if ($yaInscrito) {
                $this->dispatch('show-toast', type: 'warning', message: 'Ya estás inscrito en este curso. No puedes solicitar inscripción nuevamente.');
                $this->cerrarModal();
                return;
            }
        }

        $solicitudExistente = Solicitud::where('email_contacto', $this->email_solicitante)
            ->where('tipo', 'inscripcion')
            ->where('estado', 'pendiente')
            ->whereJsonContains('datos_adicionales->codigo_curso', $this->curso->codigo)
            ->exists();

        if ($solicitudExistente) {
            $this->dispatch('show-toast', type: 'warning', message: 'Ya tienes una solicitud pendiente para este curso.');
            return;
        }

        $user = auth()->check() ? auth()->user() : null;

        Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de inscripción al curso: ' . $this->curso->nombre,
            'mensaje' => $this->mensaje,
            'telefono' => $this->telefono_solicitante,
            'email_contacto' => $this->email_solicitante,
            'datos_adicionales' => [
                'codigo_curso' => $this->curso->codigo,
                'nombre_curso' => $this->curso->nombre,
                'motivo_inscripcion' => $this->motivacion,
                'nombre' => $this->nombre_solicitante,
                'apellido' => $this->apellido_solicitante,
                'documento_ID' => $this->documento_solicitante,
                'direccion' => $this->direccion_solicitante,
                'fecha_nacimiento' => $this->fecha_nacimiento_solicitante,
                'genero' => $this->genero_solicitante,
                'nivel_educativo' => $this->nivel_educativo_solicitante,
            ],
            'estado' => 'pendiente',
            'user_id' => $user?->id,
        ]);

        $this->dispatch('show-toast', type: 'success', message: '¡Solicitud enviada con éxito! Un administrador revisará tu solicitud y recibirás un correo con tus credenciales.');
        $this->cerrarModal();
    }

    public function toggleTema(int $index)
    {
        if (!auth()->check() || !auth()->user()->estudiante) {
            $this->dispatch('show-toast', type: 'warning', message: 'Debes iniciar sesión como estudiante para actualizar tu progreso.');
            return;
        }

        $estudiante = auth()->user()->estudiante;
        $inscripcion = $this->curso->estudiantes()->where('estudiante_id', $estudiante->codigo)->first();

        if (!$inscripcion) {
            $this->dispatch('show-toast', type: 'warning', message: 'Debes estar inscrito en este curso para registrar tu progreso.');
            return;
        }

        $progreso = $this->normalizarProgreso($this->temarioProgreso);
        $progreso[$index] = !($progreso[$index] ?? false);
        $this->temarioProgreso = $progreso;

        $this->curso->estudiantes()->updateExistingPivot($estudiante->codigo, [
            'temario_progreso' => $progreso,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Progreso del temario actualizado.');
    }

    public function eliminarCurso()
    {
        // Verificar si hay estudiantes inscritos
        if ($this->curso->estudiantes()->count() > 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No se puede eliminar el curso porque tiene estudiantes inscritos.'
            );
            return;
        }

        $this->curso->delete();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Curso eliminado correctamente.'
        );

        return redirect()->route('admin.cursos.index');
    }

    public function togglePublicacion()
    {
        $this->curso->update(['publicado' => !$this->curso->publicado]);

        $action = $this->curso->publicado ? 'publicado' : 'ocultado';
        $this->dispatch('show-toast',
            type: 'success',
            message: "Curso {$action} correctamente."
        );
    }

    public function render()
    {
        $temarioItems = $this->curso->temario_items;
        $progreso = $this->normalizarProgreso($this->temarioProgreso);

        return view('livewire.admin.cursos.mostrar-curso', [
            'estudiantes' => $this->curso->estudiantes()->with('user')->get(),
            'progresoPorcentaje' => $this->calcularProgreso($progreso, count($temarioItems)),
            'temarioItems' => $temarioItems,
        ]);
    }
}
