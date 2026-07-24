<?php
// app/Livewire/Cursos.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Curso;
use App\Models\Solicitud;
use Livewire\Attributes\Layout;

#[Layout('layouts.cursos')] // Layout dedicado para la página de cursos
class Cursos extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 9;

    // Modal de solicitud de inscripción
    public bool $mostrarModalSolicitud = false;
    public ?Curso $cursoSeleccionado = null;

    // Datos del solicitante (para usuarios no autenticados)
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

    public function render()
    {
        $cursos = Curso::where('publicado', true)
            ->when($this->search, function ($query) {
                $query->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('descripcion', 'like', '%' . $this->search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.cursos', [
            'cursos' => $cursos,
            'cursosInscritosIds' => $this->getCursosInscritosIds(),
        ]);
    }

    private function getCursosInscritosIds(): array
    {
        if (!auth()->check() || !auth()->user()->estudiante) {
            return [];
        }
        return auth()->user()->estudiante->cursos()->pluck('codigo')->toArray();
    }

    public function abrirModalSolicitud($cursoCodigo)
    {
        try {
            $this->cursoSeleccionado = Curso::where('codigo', $cursoCodigo)->firstOrFail();

            // Si el usuario está autenticado, verificar que no esté ya inscrito
            if (auth()->check()) {
                $user = auth()->user();
                if ($user->estudiante) {
                    $yaInscrito = $user->estudiante->cursos()->where('codigo', $cursoCodigo)->exists();
                    if ($yaInscrito) {
                        $this->dispatch('show-toast',
                            type: 'warning',
                            message: 'Ya estás inscrito en este curso. No puedes solicitar inscripción nuevamente.'
                        );
                        return;
                    }
                }
            }

            $this->mostrarModalSolicitud = true;

            // Si el usuario está autenticado, precargar sus datos
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
        } catch (\Exception $e) {
            $this->dispatch('show-toast', type: 'error', message: 'Error al abrir el formulario de solicitud.');
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModalSolicitud = false;
        $this->cursoSeleccionado = null;
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

        if (!$this->cursoSeleccionado) {
            $this->dispatch('show-toast', type: 'error', message: 'Curso no encontrado.');
            return;
        }

        // Verificar si el usuario autenticado ya está inscrito en este curso
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->estudiante) {
                $yaInscrito = $user->estudiante->cursos()->where('codigo', $this->cursoSeleccionado->codigo)->exists();
                if ($yaInscrito) {
                    $this->dispatch('show-toast',
                        type: 'warning',
                        message: 'Ya estás inscrito en este curso. No puedes solicitar inscripción nuevamente.'
                    );
                    $this->cerrarModal();
                    return;
                }
            }
        }

        // Verificar si ya existe una solicitud pendiente con el mismo email y curso
        $solicitudExistente = Solicitud::where('email_contacto', $this->email_solicitante)
            ->where('tipo', 'inscripcion')
            ->where('estado', 'pendiente')
            ->whereJsonContains('datos_adicionales->codigo_curso', $this->cursoSeleccionado->codigo)
            ->exists();

        if ($solicitudExistente) {
            $this->dispatch('show-toast', type: 'warning', message: 'Ya tienes una solicitud pendiente para este curso. Espera a que sea revisada.');
            return;
        }

        $user = auth()->check() ? auth()->user() : null;

        // Crear la solicitud de inscripción
        Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de inscripción al curso: ' . $this->cursoSeleccionado->nombre,
            'mensaje' => $this->mensaje,
            'telefono' => $this->telefono_solicitante,
            'email_contacto' => $this->email_solicitante,
            'datos_adicionales' => [
                'codigo_curso' => $this->cursoSeleccionado->codigo,
                'nombre_curso' => $this->cursoSeleccionado->nombre,
                'motivo_inscripcion' => $this->motivacion,
                // Datos personales del solicitante
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
}
