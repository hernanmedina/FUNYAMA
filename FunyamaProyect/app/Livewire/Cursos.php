<?php
// app/Livewire/Cursos.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Curso;
use App\Models\Solicitud;
use Livewire\Attributes\Layout;

#[Layout('layouts.public')] // Usar el layout público
class Cursos extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 9;

    // Modal de solicitud de inscripción
    public bool $mostrarModalSolicitud = false;
    public ?Curso $cursoSeleccionado = null;
    public string $mensaje = '';
    public string $motivacion = '';
    public string $telefono = '';

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
            'cursos' => $cursos
        ]);
    }

    public function abrirModalSolicitud($cursoCodigo)
    {
        \Log::info('abrirModalSolicitud llamado', ['cursoCodigo' => $cursoCodigo]);

        try {
            $this->cursoSeleccionado = Curso::where('codigo', $cursoCodigo)->firstOrFail();
            \Log::info('Curso encontrado', ['curso' => $this->cursoSeleccionado->nombre]);

            $this->mostrarModalSolicitud = true;
            $this->reset(['mensaje', 'motivacion', 'telefono']);

            \Log::info('Modal abierto correctamente');
        } catch (\Exception $e) {
            \Log::error('Error en abrirModalSolicitud', [
                'cursoCodigo' => $cursoCodigo,
                'error' => $e->getMessage()
            ]);

            $this->dispatch('show-toast', type: 'error', message: 'Error al abrir el formulario de solicitud.');
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModalSolicitud = false;
        $this->cursoSeleccionado = null;
        $this->reset(['mensaje', 'motivacion', 'telefono']);
    }

    public function enviarSolicitud()
    {
        \Log::info('enviarSolicitud llamado', [
            'curso' => $this->cursoSeleccionado?->codigo,
            'mensaje' => strlen($this->mensaje),
            'motivacion' => $this->motivacion
        ]);

        // Validación
        $this->validate([
            'mensaje' => 'required|min:10',
            'motivacion' => 'required',
            'telefono' => 'nullable|string|min:7'
        ], [
            'mensaje.required' => 'El mensaje es obligatorio.',
            'mensaje.min' => 'El mensaje debe tener al menos 10 caracteres.',
            'motivacion.required' => 'Debes seleccionar una motivación.',
            'telefono.min' => 'El teléfono debe tener al menos 7 caracteres.'
        ]);

        \Log::info('Validación pasada');

        if (!$this->cursoSeleccionado) {
            \Log::error('cursoSeleccionado es null');
            $this->dispatch('show-toast', type: 'error', message: 'Curso no encontrado.');
            return;
        }

        // Verificar si ya existe una solicitud pendiente para este curso y usuario
        $solicitudExistente = Solicitud::where('user_id', auth()->id())
            ->where('tipo', 'inscripcion')
            ->where('estado', 'pendiente')
            ->whereJsonContains('datos_adicionales->codigo_curso', $this->cursoSeleccionado->codigo)
            ->exists();

        if ($solicitudExistente) {
            \Log::info('Solicitud ya existe');
            $this->dispatch('show-toast', type: 'warning', message: 'Ya tienes una solicitud pendiente para este curso.');
            return;
        }

        \Log::info('Creando solicitud');

        // Crear la solicitud
        Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de inscripción al curso: ' . $this->cursoSeleccionado->nombre,
            'mensaje' => $this->mensaje,
            'telefono' => $this->telefono,
            'email_contacto' => auth()->user()->email,
            'datos_adicionales' => [
                'codigo_curso' => $this->cursoSeleccionado->codigo,
                'nombre_curso' => $this->cursoSeleccionado->nombre,
                'estudiante_codigo' => auth()->user()->estudiante->codigo ?? null,
                'motivo_inscripcion' => $this->motivacion
            ],
            'estado' => 'pendiente',
            'user_id' => auth()->id()
        ]);

        \Log::info('Solicitud creada');

        $this->dispatch('show-toast', type: 'success', message: 'Solicitud de inscripción enviada correctamente. El administrador la revisará pronto.');
        $this->cerrarModal();
    }
}
