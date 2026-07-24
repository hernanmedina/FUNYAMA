<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Curso;
use App\Models\Solicitud;

#[Layout('layouts.public')]
class CursoDetalle extends Component
{
    public Curso $curso;

    // Modal de solicitud de inscripción
    public bool $mostrarModalSolicitud = false;
    public array $temarioProgreso = [];

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

    // Datos de la solicitud
    public string $mensaje = '';
    public string $motivacion = '';

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

    public function mount(Curso $curso)
    {
        $this->curso = $curso;

        if (auth()->check() && auth()->user()->estudiante) {
            $inscripcion = $curso->estudiantes()
                ->where('estudiante_id', auth()->user()->estudiante->codigo)
                ->first();

            $this->temarioProgreso = $inscripcion?->pivot?->temario_progreso ?? [];
        }
    }

    public function abrirModalSolicitud()
    {
        $this->mostrarModalSolicitud = true;

        if (auth()->check()) {
            $user = auth()->user();
            $this->nombre_solicitante = $user->name;
            $this->apellido_solicitante = $user->apellido;
            $this->email_solicitante = $user->email;
            $this->telefono_solicitante = $user->telefono;
            $this->documento_solicitante = $user->documento_ID;
            $this->direccion_solicitante = $user->direccion;

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

        $solicitudExistente = Solicitud::where('email_contacto', $this->email_solicitante)
            ->where('tipo', 'inscripcion')
            ->where('estado', 'pendiente')
            ->whereJsonContains('datos_adicionales->codigo_curso', $this->curso->codigo)
            ->exists();

        if ($solicitudExistente) {
            $this->dispatch('show-toast', type: 'warning', message: 'Ya tienes una solicitud pendiente para este curso. Espera a que sea revisada.');
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

    public function toggleTema($index)
    {
        if (!auth()->check() || !auth()->user()->estudiante) {
            return;
        }

        $estudiante = auth()->user()->estudiante;
        $inscripcion = $this->curso->estudiantes()->where('estudiante_id', $estudiante->codigo)->first();

        if (!$inscripcion) {
            return;
        }

        $progreso = is_array($this->temarioProgreso) ? $this->temarioProgreso : [];
        $progreso[$index] = isset($progreso[$index]) ? !$progreso[$index] : true;
        $this->temarioProgreso = $progreso;

        $this->curso->estudiantes()->updateExistingPivot($estudiante->codigo, [
            'temario_progreso' => $progreso,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Progreso actualizado.');
    }

    public function render()
    {
        return view('livewire.curso-detalle', [
            'estudiantes' => $this->curso->estudiantes()->with('user')->get(),
        ]);
    }
}
