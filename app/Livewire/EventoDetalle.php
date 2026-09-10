<?php

namespace App\Livewire;

use App\Models\Evento;
use App\Models\InscripcionEvento;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.public')]
class EventoDetalle extends Component
{
    public Evento $evento;

    // Modal de inscripción
    public bool $mostrarModalInscripcion = false;

    // Datos del inscrito
    public string $nombre = '';

    public string $apellido = '';

    public string $email = '';

    public string $telefono = '';

    public string $documento = '';

    public function mount(Evento $evento): void
    {
        abort_unless($evento->publicado, 404);

        $this->evento = $evento;
    }

    /**
     * Abre el formulario de inscripción para el evento.
     */
    public function abrirInscripcion(): void
    {
        $evento = $this->evento;

        if (! $evento->publicado) {
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

    public function cerrarModalInscripcion(): void
    {
        $this->mostrarModalInscripcion = false;
        $this->reset(['nombre', 'apellido', 'email', 'telefono', 'documento']);
    }

    protected function rules(): array
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
    public function confirmarInscripcion(): void
    {
        $this->validate();

        $evento = $this->evento;

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

        // Refrescar el evento para reflejar el nuevo contador de inscritos
        $this->evento->refresh();

        $this->cerrarModalInscripcion();
    }

    public function render()
    {
        $relacionados = Evento::where('publicado', true)
            ->where('idEvento', '!=', $this->evento->idEvento)
            ->where('fecha', '>=', now())
            ->orderBy('fecha')
            ->take(3)
            ->get();

        return view('livewire.evento-detalle', [
            'relacionados' => $relacionados,
        ]);
    }
}
