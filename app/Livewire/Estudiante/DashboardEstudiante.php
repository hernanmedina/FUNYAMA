<?php

namespace App\Livewire\Estudiante;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Solicitud;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]

class DashboardEstudiante extends Component
{
    public $cursosInscritos;

    public $cursosFinalizados;

    public $cursosDisponibles;

    public $estadisticas;

    public $certificadosRecientes;

    // Modal de inscripción
    public bool $mostrarModal = false;

    public ?string $cursoSeleccionado = null;

    public ?Curso $cursoData = null;

    // Modal de opinión del estudiante
    public bool $mostrarModalOpinion = false;

    public ?string $cursoOpinionId = null;

    public ?string $cursoOpinionNombre = null;

    public int $ratingOpinion = 0;

    public string $textoOpinion = '';

    public string $nombre = '';

    public string $apellido = '';

    public string $documento_identidad = '';

    public string $direccion = '';

    public ?string $fecha_nacimiento = '';

    public ?string $genero = '';

    public ?string $nivel_educativo = '';

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
            // Cursos en los que está inscrito el estudiante (todos)
            $todosCursos = $estudiante->cursos()
                ->withPivot('estado', 'progreso', 'temario_progreso', 'fecha_inscripcion', 'calificacion', 'comentario_calificacion', 'fecha_completado')
                ->orderBy('curso_estudiante.fecha_inscripcion', 'desc')
                ->get();

            $todosCursos->each(function ($curso) {
                $progreso = $this->calcularProgresoCurso($curso);
                $curso->porcentaje_progreso = $progreso;
                if ($curso->pivot) {
                    $curso->pivot->progreso = $progreso;
                }
            });

            // Separar: cursos activos (no completados) vs finalizados
            $this->cursosInscritos = $todosCursos
                ->filter(fn ($c) => ($c->porcentaje_progreso ?? 0) < 100)
                ->take(5)
                ->values();

            $this->cursosFinalizados = $todosCursos
                ->filter(fn ($c) => ($c->porcentaje_progreso ?? 0) >= 100)
                ->take(5)
                ->values();

            // Cursos disponibles (no inscritos y publicados)
            $cursosInscritosIds = $estudiante->cursos()->pluck('codigo')->toArray() ?? [];

            $this->cursosDisponibles = Curso::where('publicado', true)
                ->whereNotIn('codigo', $cursosInscritosIds)
                ->where('cupo_disponible', '>', 0)
                ->orderBy('fecha_inicio', 'asc')
                ->take(6)
                ->get();

            // Estadísticas
            $cursos = $estudiante->cursos()->withPivot('estado', 'progreso', 'temario_progreso', 'fecha_inscripcion', 'calificacion', 'comentario_calificacion', 'fecha_completado')->get();
            $cursos->each(function ($curso) {
                $curso->porcentaje_progreso = $this->calcularProgresoCurso($curso);
            });

            $this->estadisticas = [
                'total_cursos' => $cursos->count(),
                'cursos_completados' => $cursos->where('porcentaje_progreso', 100)->count(),
                'cursos_en_progreso' => $cursos->where('porcentaje_progreso', '<', 100)->count(),
                'promedio_progreso' => $cursos->avg('porcentaje_progreso') ?? 0,
            ];

            // Certificados recientes (con archivo PDF subido)
            $this->certificadosRecientes = $estudiante->certificados()
                ->with('curso')
                ->whereNotNull('archivo_path')
                ->orderBy('fecha_emision', 'desc')
                ->take(4)
                ->get();
        } else {
            // Inicializar vacío si no hay estudiante
            $this->cursosInscritos = collect();
            $this->cursosFinalizados = collect();
            $this->cursosDisponibles = collect();
            $this->estadisticas = [
                'total_cursos' => 0,
                'cursos_completados' => 0,
                'cursos_en_progreso' => 0,
                'promedio_progreso' => 0,
            ];

            $this->certificadosRecientes = collect();
        }
    }

    protected function calcularProgresoCurso($curso): float
    {
        $temarioItems = $curso->temario_items ?? [];
        $total = count($temarioItems);

        if ($total === 0) {
            return (float) ($curso->pivot->progreso ?? 0);
        }

        $progreso = $curso->pivot->temario_progreso ?? [];
        $progreso = is_string($progreso) ? json_decode($progreso, true) : $progreso;
        $progreso = is_array($progreso) ? $progreso : [];

        $completados = count(array_filter($progreso, fn ($valor) => $valor === true));

        return round(($completados / $total) * 100, 2);
    }

    public function inscribirCurso($codigo)
    {
        $curso = Curso::where('codigo', $codigo)->first();

        if (! $curso) {
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

        $user = Auth::user();
        $this->nombre = $user->name ?? '';
        $this->apellido = $user->apellido ?? '';
        $this->email_contacto = $user->email ?? '';
        $this->telefono = $user->telefono ?? '';
        $this->documento_identidad = $user->documento_ID ?? '';
        $this->direccion = $user->direccion ?? '';

        if ($user?->estudiante) {
            $this->fecha_nacimiento = $user->estudiante->fecha_nacimiento?->format('Y-m-d') ?? '';
            $this->genero = $user->estudiante->genero ?? '';
            $this->nivel_educativo = $user->estudiante->nivel_educativo ?? '';
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
        $this->reset(['mensaje', 'telefono', 'email_contacto', 'motivoInscripcion', 'nombre', 'apellido', 'documento_identidad', 'direccion', 'fecha_nacimiento', 'genero', 'nivel_educativo']);
        $this->resetValidation();
    }

    // ─── Modal de Opinión del Estudiante ───────────────────────────────

    public function abrirModalOpinion(string $cursoId, string $cursoNombre): void
    {
        $user = Auth::user();

        if (! $user?->estudiante) {
            return;
        }

        $pivot = DB::table('curso_estudiante')
            ->where('curso_id', $cursoId)
            ->where('estudiante_id', $user->estudiante->codigo)
            ->first();

        $this->cursoOpinionId = $cursoId;
        $this->cursoOpinionNombre = $cursoNombre;
        $this->ratingOpinion = (int) ($pivot?->rating_estudiante ?? 0);
        $this->textoOpinion = $pivot?->opinion_estudiante ?? '';
        $this->mostrarModalOpinion = true;
    }

    public function cerrarModalOpinion(): void
    {
        $this->mostrarModalOpinion = false;
        $this->reset(['cursoOpinionId', 'cursoOpinionNombre', 'ratingOpinion', 'textoOpinion']);
    }

    public function setRating(int $rating): void
    {
        $this->ratingOpinion = max(1, min(5, $rating));
    }

    public function enviarOpinion(): void
    {
        $this->validate([
            'ratingOpinion' => ['required', 'integer', 'min:1', 'max:5'],
            'textoOpinion' => ['required', 'string', 'min:10', 'max:1000'],
            'cursoOpinionId' => ['required', 'string'],
        ]);

        $user = Auth::user();

        if (! $user?->estudiante) {
            return;
        }

        DB::table('curso_estudiante')
            ->where('curso_id', $this->cursoOpinionId)
            ->where('estudiante_id', $user->estudiante->codigo)
            ->update([
                'rating_estudiante' => $this->ratingOpinion,
                'opinion_estudiante' => $this->textoOpinion,
                'updated_at' => now(),
            ]);

        $this->dispatch('show-toast', type: 'success', message: '¡Gracias por tu opinión! Tu retroalimentación ha sido guardada.');
        $this->cerrarModalOpinion();
        $this->mount();
    }

    public function enviarSolicitud()
    {
        $this->validate();

        $user = Auth::user();
        $estudiante = $user->estudiante;

        // Validar que no esté ya inscrito (doble protección)
        $yaInscrito = $estudiante->cursos()->where('codigo', $this->cursoSeleccionado)->exists();
        if ($yaInscrito) {
            $this->dispatch('show-toast',
                type: 'warning',
                message: 'Ya estás inscrito en este curso. No puedes solicitar inscripción nuevamente.'
            );
            $this->cerrarModal();

            return;
        }

        // Crear solicitud de inscripción
        Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de inscripción al curso: '.$this->cursoData->nombre,
            'mensaje' => $this->mensaje,
            'telefono' => $this->telefono,
            'email_contacto' => $this->email_contacto,
            'datos_adicionales' => [
                'codigo_curso' => $this->cursoSeleccionado,
                'nombre_curso' => $this->cursoData->nombre,
                'motivo_inscripcion' => $this->motivoInscripcion,
                'estudiante_codigo' => $estudiante->codigo,
                'nombre' => $this->nombre,
                'apellido' => $this->apellido,
                'documento_ID' => $this->documento_identidad,
                'direccion' => $this->direccion,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'genero' => $this->genero,
                'nivel_educativo' => $this->nivel_educativo,
                'email_contacto' => $this->email_contacto,
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
