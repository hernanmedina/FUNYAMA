<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;

#[Layout('layouts.app')] 

class DashboardEstudiante extends Component
{
    public $cursosInscritos;
    public $cursosDisponibles;
    public $estadisticas;

    // Modal de inscripción
    public bool $mostrarModal = false;
    public ?string $cursoSeleccionado = null;
    public ?Curso $cursoData = null;

    #[Validate('required|string')]
    public string $mensaje = '';
    
    #[Validate('required|regex:/^[0-9]{10}$/')]
    public string $telefono = '';
    
    #[Validate('required|email')]
    public string $email_contacto = '';
    
    #[Validate('nullable|string')]
    public string $motivoInscripcion = '';

    public function mount()
    {
        $user = Auth::user();
        $estudiante = $user?->estudiante;

        if ($estudiante) {
            // Cursos en los que está inscrito el estudiante
            $this->cursosInscritos = $estudiante->cursos()
                ->withPivot('estado', 'progreso', 'fecha_inscripcion')
                ->orderBy('curso_estudiante.fecha_inscripcion', 'desc')
                ->take(5)
                ->get();

            // Cursos disponibles (no inscritos y publicados)
            $cursosInscritosIds = $estudiante->cursos()->pluck('codigo')->toArray() ?? [];
            
            $this->cursosDisponibles = Curso::where('publicado', true)
                ->whereNotIn('codigo', $cursosInscritosIds)
                ->where('cupo_disponible', '>', 0)
                ->orderBy('fecha_inicio', 'asc')
                ->take(6)
                ->get();

            // Estadísticas
            $this->estadisticas = [
                'total_cursos' => $estudiante->cursos()->count(),
                'cursos_completados' => $estudiante->cursos()->wherePivot('estado', 'completado')->count(),
                'cursos_en_progreso' => $estudiante->cursos()->wherePivot('estado', 'en_progreso')->count(),
                'promedio_progreso' => $estudiante->cursos()->avg('curso_estudiante.progreso') ?? 0,
            ];
        } else {
            // Inicializar vacío si no hay estudiante
            $this->cursosInscritos = collect();
            $this->cursosDisponibles = collect();
            $this->estadisticas = [
                'total_cursos' => 0,
                'cursos_completados' => 0,
                'cursos_en_progreso' => 0,
                'promedio_progreso' => 0,
            ];
        }
    }

    public function inscribirCurso($codigo)
    {
        $curso = Curso::where('codigo', $codigo)->first();

        if (!$curso) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Curso no encontrado.'
            );
            return;
        }

        if ($curso->cupo_disponible <= 0) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'No hay cupos disponibles en este curso.'
            );
            return;
        }

        // Verificar si ya está inscrito
        $estudiante = Auth::user()->estudiante;
        if ($estudiante->cursos()->where('codigo', $codigo)->exists()) {
            $this->dispatch('show-toast',
                type: 'warning',
                message: 'Ya estás inscrito en este curso.'
            );
            return;
        }

        // Abrir modal para diligenciar solicitud
        $this->cursoSeleccionado = $codigo;
        $this->cursoData = $curso;
        $this->mostrarModal = true;
        $this->resetValidation();
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->cursoSeleccionado = null;
        $this->cursoData = null;
        $this->reset(['mensaje', 'telefono', 'email_contacto', 'motivoInscripcion']);
        $this->resetValidation();
    }

    public function enviarSolicitud()
    {
        $this->validate();

        $user = Auth::user();
        $estudiante = $user->estudiante;

        // Crear solicitud de inscripción
        Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de inscripción al curso: ' . $this->cursoData->nombre,
            'mensaje' => $this->mensaje,
            'telefono' => $this->telefono,
            'email_contacto' => $this->email_contacto,
            'datos_adicionales' => [
                'codigo_curso' => $this->cursoSeleccionado,
                'nombre_curso' => $this->cursoData->nombre,
                'motivo_inscripcion' => $this->motivoInscripcion,
                'estudiante_codigo' => $estudiante->codigo, // Usar 'codigo' como PK
            ],
            'estado' => 'pendiente',
            'user_id' => $user->id,
        ]);

        $this->dispatch('show-toast',
            type: 'success',
            message: '¡Solicitud de inscripción enviada! El administrador revisará tu solicitud pronto.'
        );

        $this->cerrarModal();
        $this->mount();
    }

    public function render()
    {
        return view('livewire.estudiante.dashboard-estudiante')
            ->layout('layouts.app');
    }
}
