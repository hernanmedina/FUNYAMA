<?php

namespace App\Livewire;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class CalendarioEventos extends Component
{
    public $eventoSeleccionado = null;

    public $meses = [];

    public $mesActual = '';

    public $anioActual = '';

    public $eventosFiltrados = [];

    // Modal de inscripción
    public bool $mostrarModalInscripcion = false;

    public ?Evento $eventoInscripcion = null;

    // Datos del inscrito
    public string $nombre = '';

    public string $apellido = '';

    public string $email = '';

    public string $telefono = '';

    public string $documento = '';

    public function mount()
    {
        $now = now();

        $this->mesActual = $now->month;
        $this->anioActual = $now->year;

        $this->cargarEventos();
    }

    public function cargarEventos()
    {
        $inicio = Carbon::create($this->anioActual, $this->mesActual, 1)
            ->startOfMonth()
            ->startOfDay();

        $fin = Carbon::create($this->anioActual, $this->mesActual, 1)
            ->endOfMonth()
            ->endOfDay();

        $this->eventosFiltrados = Evento::where('publicado', true)
            ->whereBetween('fecha', [$inicio, $fin])
            ->orderBy('fecha')
            ->get();
    }

    public function seleccionarEvento(int $id)
    {
        $this->eventoSeleccionado = Evento::find($id);
    }

    public function cerrarModal()
    {
        $this->eventoSeleccionado = null;
    }

    public function siguienteMes()
    {
        $fecha = Carbon::create($this->anioActual, $this->mesActual, 1)
            ->addMonth();

        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;

        $this->cargarEventos();
    }

    public function mesAnterior()
    {
        $fecha = Carbon::create($this->anioActual, $this->mesActual, 1)
            ->subMonth();

        $this->mesActual = $fecha->month;
        $this->anioActual = $fecha->year;

        $this->cargarEventos();
    }

    /**
     * Abre el formulario de inscripción para un evento.
     */
    public function abrirInscripcion(int $id)
    {
        $evento = Evento::find($id);

        if (! $evento || ! $evento->publicado) {
            $this->dispatch('show-toast', type: 'error', message: 'El evento no está disponible.');

            return;
        }

        // Verificar disponibilidad de cupos
        if ($evento->cupo_maximo !== null && $evento->inscritos_actual >= $evento->cupo_maximo) {
            $this->dispatch('show-toast', type: 'warning', message: 'Lo sentimos, el evento ya no tiene cupos disponibles.');

            return;
        }

        // Verificar si el usuario autenticado ya está inscrito
        if (auth()->check()) {
            $yaInscrito = InscripcionEvento::where('idEvento', $evento->idEvento)
                ->where('user_id', auth()->id())
                ->where('estado', 'confirmada')
                ->exists();

            if ($yaInscrito) {
                $this->dispatch('show-toast', type: 'warning', message: 'Ya estás inscrito en este evento.');

                return;
            }
        }

        $this->eventoInscripcion = $evento;
        $this->mostrarModalInscripcion = true;

        // Precargar datos si el usuario está autenticado
        if (auth()->check()) {
            $user = auth()->user();
            $this->nombre = $user->name;
            $this->apellido = $user->apellido ?? '';
            $this->email = $user->email;
            $this->telefono = $user->telefono ?? '';
            $this->documento = $user->documento_ID ?? '';
        } else {
            $this->reset(['nombre', 'apellido', 'email', 'telefono', 'documento']);
        }
    }

    public function cerrarModalInscripcion()
    {
        $this->mostrarModalInscripcion = false;
        $this->eventoInscripcion = null;
        $this->reset(['nombre', 'apellido', 'email', 'telefono', 'documento']);
    }

    protected function rules()
    {
        return [
            'nombre' => 'required|string|max:255',
            'apellido' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'telefono' => 'nullable|string|max:30',
            'documento' => 'nullable|string|max:30',
        ];
    }

    protected $messages = [
        'nombre.required' => 'El nombre es obligatorio.',
        'email.required' => 'El email es obligatorio.',
        'email.email' => 'Debes ingresar un email válido.',
    ];

    /**
     * Confirma la inscripción al evento de forma directa (sin aprobación de admin).
     */
    public function confirmarInscripcion()
    {
        $this->validate();

        if (! $this->eventoInscripcion) {
            $this->dispatch('show-toast', type: 'error', message: 'Evento no encontrado.');

            return;
        }

        $evento = $this->eventoInscripcion;

        // Verificar que el evento siga publicado
        if (! $evento->publicado) {
            $this->dispatch('show-toast', type: 'error', message: 'El evento ya no está disponible.');

            return;
        }

        // Verificar si el usuario autenticado ya está inscrito
        if (auth()->check()) {
            $yaInscrito = InscripcionEvento::where('idEvento', $evento->idEvento)
                ->where('user_id', auth()->id())
                ->where('estado', 'confirmada')
                ->exists();

            if ($yaInscrito) {
                $this->dispatch('show-toast', type: 'warning', message: 'Ya estás inscrito en este evento.');

                return;
            }
        }

        // Verificar si ya existe una inscripción confirmada con el mismo email para este evento
        $yaInscritoEmail = InscripcionEvento::where('idEvento', $evento->idEvento)
            ->where('email', $this->email)
            ->where('estado', 'confirmada')
            ->exists();

        if ($yaInscritoEmail) {
            $this->dispatch('show-toast', type: 'warning', message: 'Ya existe una inscripción registrada con este email para el evento.');

            return;
        }

        // Registrar la inscripción de forma atómica para evitar condiciones de carrera
        // sobre el cupo máximo (ver reglas de arquitectura del proyecto).
        try {
            DB::transaction(function () use ($evento) {
                // Bloquear la fila del evento para evitar sobre-inscripción
                $eventoBloqueado = Evento::whereKey($evento->idEvento)->lockForUpdate()->first();

                if (! $eventoBloqueado) {
                    throw new \Exception('El evento ya no existe.');
                }

                if ($eventoBloqueado->cupo_maximo !== null && $eventoBloqueado->inscritos_actual >= $eventoBloqueado->cupo_maximo) {
                    throw new \Exception('cupos_agotados');
                }

                // Crear la inscripción
                InscripcionEvento::create([
                    'idEvento' => $eventoBloqueado->idEvento,
                    'user_id' => auth()->id(),
                    'nombre' => $this->nombre,
                    'apellido' => $this->apellido,
                    'email' => $this->email,
                    'telefono' => $this->telefono,
                    'documento' => $this->documento,
                    'estado' => 'confirmada',
                ]);

                // Incrementar el contador de inscritos
                $eventoBloqueado->increment('inscritos_actual');
            });
        } catch (\Exception $e) {
            if ($e->getMessage() === 'cupos_agotados') {
                $this->dispatch('show-toast', type: 'warning', message: 'Lo sentimos, el evento ya no tiene cupos disponibles.');
            } else {
                $this->dispatch('show-toast', type: 'error', message: 'No se pudo completar la inscripción. Inténtalo de nuevo.');
            }

            return;
        }

        $this->dispatch('show-toast', type: 'success', message: '¡Inscripción confirmada! Te esperamos en el evento.');

        // Cerrar el modal de inscripción y el de detalles para volver a la
        // vista de eventos disponibles.
        $this->cerrarModalInscripcion();
        $this->cerrarModal();
    }

    public function render()
    {
        $eventos = Evento::where('publicado', true)
            ->orderBy('fecha')
            ->take(20)
            ->get();

        return view('livewire.calendario-eventos', [
            'eventos' => $eventos,
        ]);
    }
}
