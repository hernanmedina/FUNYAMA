<?php

namespace Tests\Feature;

use App\Livewire\Admin\Solicitudes\SolicitudesInscripcion;
use App\Models\Administrador;
use App\Models\Evento;
use App\Models\InscripcionEvento;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SolicitudesInscripcionTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);
    }

    private function crearEvento(): Evento
    {
        $admin = Administrador::create([
            'user_id' => $this->crearAdmin()->id,
            'departamento' => 'General',
            'cargo' => 'Administrador',
            'super_admin' => true,
        ]);

        return Evento::create([
            'titulo' => 'Evento de prueba',
            'slug' => 'evento-de-prueba',
            'descripcion' => 'Descripción del evento',
            'fecha' => now()->addDays(10)->toDateString(),
            'hora_inicio' => '10:00',
            'hora_fin' => '12:00',
            'ubicacion' => 'Auditorio',
            'cupo_maximo' => 50,
            'inscritos_actual' => 0,
            'costo' => 0,
            'tipo_evento' => 'presencial',
            'publicado' => true,
            'creado_por_admin' => $admin->idAdmin,
        ]);
    }

    public function test_muestra_solicitudes_de_curso_e_inscripciones_a_eventos(): void
    {
        $admin = $this->crearAdmin();
        $evento = $this->crearEvento();

        Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de curso',
            'mensaje' => 'Quiero inscribirme',
            'email_contacto' => 'curso@example.com',
            'estado' => 'pendiente',
            'datos_adicionales' => [
                'nombre' => 'Juan',
                'apellido' => 'Pérez',
                'nombre_curso' => 'Curso de Laravel',
            ],
            'user_id' => $admin->id,
        ]);

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'nombre' => 'María',
            'apellido' => 'Gómez',
            'email' => 'evento@example.com',
            'estado' => 'confirmada',
        ]);

        $this->actingAs($admin);

        Livewire::test(SolicitudesInscripcion::class)
            ->set('filtroEstado', 'todas')
            ->assertSee('Juan')
            ->assertSee('Curso de Laravel')
            ->assertSee('María')
            ->assertSee('Evento de prueba')
            ->assertSee('Evento')
            ->assertSee('Curso');
    }

    public function test_filtra_inscripciones_a_eventos_confirmadas_como_resueltas(): void
    {
        $admin = $this->crearAdmin();
        $evento = $this->crearEvento();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'nombre' => 'Carlos',
            'apellido' => 'Ruiz',
            'email' => 'carlos@example.com',
            'estado' => 'confirmada',
        ]);

        $this->actingAs($admin);

        Livewire::test(SolicitudesInscripcion::class)
            ->set('filtroEstado', 'resuelta')
            ->assertSee('Carlos')
            ->assertSee('Aprobada');
    }

    public function test_no_muestra_inscripciones_a_eventos_canceladas_en_pendientes(): void
    {
        $admin = $this->crearAdmin();
        $evento = $this->crearEvento();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'nombre' => 'Pedro',
            'apellido' => 'López',
            'email' => 'pedro@example.com',
            'estado' => 'cancelada',
        ]);

        $this->actingAs($admin);

        Livewire::test(SolicitudesInscripcion::class)
            ->set('filtroEstado', 'pendiente')
            ->assertDontSee('Pedro');
    }

    public function test_busqueda_encuentra_inscripciones_a_eventos_por_nombre(): void
    {
        $admin = $this->crearAdmin();
        $evento = $this->crearEvento();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'nombre' => 'Lucía',
            'apellido' => 'Fernández',
            'email' => 'lucia@example.com',
            'estado' => 'confirmada',
        ]);

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'nombre' => 'Roberto',
            'apellido' => 'Silva',
            'email' => 'roberto@example.com',
            'estado' => 'confirmada',
        ]);

        $this->actingAs($admin);

        Livewire::test(SolicitudesInscripcion::class)
            ->set('filtroEstado', 'todas')
            ->set('search', 'Lucía')
            ->assertSee('Lucía')
            ->assertDontSee('Roberto');
    }
}
