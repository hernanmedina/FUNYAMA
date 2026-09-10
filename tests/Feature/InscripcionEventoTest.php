<?php

namespace Tests\Feature;

use App\Livewire\Admin\Eventos\IndexEventos;
use App\Livewire\Admin\Eventos\IngresosEventos;
use App\Livewire\Admin\Eventos\InscripcionesEvento;
use App\Livewire\Admin\Eventos\InscritosEventos;
use App\Livewire\Admin\Eventos\MostrarEvento;
use App\Livewire\Admin\Eventos\PagosPendientesEventos;
use App\Livewire\CalendarioEventos;
use App\Livewire\Estudiante\MisEventos;
use App\Livewire\EventoDetalle;
use App\Models\Administrador;
use App\Models\Evento;
use App\Models\InscripcionEvento;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InscripcionEventoTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function crearAdministrador(User $admin): Administrador
    {
        return Administrador::create([
            'user_id' => $admin->id,
            'departamento' => 'Dirección',
            'cargo' => 'Administrador',
            'super_admin' => true,
            'fecha_ingreso' => now(),
        ]);
    }

    private function crearEvento(array $overrides = []): Evento
    {
        $admin = $this->crearAdmin();
        $administrador = $this->crearAdministrador($admin);

        return Evento::create(array_merge([
            'titulo' => 'Evento de prueba',
            'slug' => 'evento-de-prueba-'.fake()->unique()->numerify('####'),
            'descripcion' => 'Descripción del evento de prueba.',
            'fecha' => now()->addDays(10),
            'hora_inicio' => '10:00:00',
            'hora_fin' => '12:00:00',
            'ubicacion' => 'Auditorio principal',
            'tipo_evento' => 'presencial',
            'costo' => 0,
            'publicado' => true,
            'creado_por_admin' => $administrador->idAdmin,
        ], $overrides));
    }

    private function crearEstudianteUser(): User
    {
        return User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Test',
            'role' => 'estu',
            'email' => 'estudiante'.fake()->unique()->numerify('####').'@test.com',
        ]);
    }

    public function test_visitante_puede_inscribirse_a_un_evento_publico(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);

        Livewire::test(CalendarioEventos::class)
            ->call('abrirInscripcion', $evento->idEvento)
            ->assertSet('mostrarModalInscripcion', true)
            ->set('nombre', 'Juan')
            ->set('apellido', 'Pérez')
            ->set('email', 'juan@example.com')
            ->set('telefono', '3001234567')
            ->set('documento', '123456789')
            ->call('confirmarInscripcion')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idEvento' => $evento->idEvento,
            'email' => 'juan@example.com',
            'nombre' => 'Juan',
            'estado' => 'confirmada',
        ]);

        // El contador de inscritos debe incrementarse
        $this->assertDatabaseHas('eventos', [
            'idEvento' => $evento->idEvento,
            'inscritos_actual' => 1,
        ]);
    }

    public function test_estudiante_autenticado_puede_inscribirse_y_queda_vinculado_a_su_usuario(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $user = $this->crearEstudianteUser();

        $this->actingAs($user);

        Livewire::test(CalendarioEventos::class)
            ->call('abrirInscripcion', $evento->idEvento)
            ->assertSet('mostrarModalInscripcion', true)
            // Los datos del usuario autenticado se precargan
            ->assertSet('nombre', $user->name)
            ->assertSet('email', $user->email)
            ->call('confirmarInscripcion')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'estado' => 'confirmada',
        ]);
    }

    public function test_no_se_puede_inscribir_si_el_evento_no_tiene_cupos(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 1, 'inscritos_actual' => 1]);

        Livewire::test(CalendarioEventos::class)
            ->call('abrirInscripcion', $evento->idEvento)
            ->assertSet('mostrarModalInscripcion', false)
            ->assertDispatched('show-toast', type: 'warning');

        $this->assertDatabaseCount('inscripcion_eventos', 0);
    }

    public function test_estudiante_no_puede_inscribirse_dos_veces_al_mismo_evento(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($user);

        Livewire::test(CalendarioEventos::class)
            ->call('abrirInscripcion', $evento->idEvento)
            ->assertSet('mostrarModalInscripcion', false)
            ->assertDispatched('show-toast', type: 'warning');

        $this->assertDatabaseCount('inscripcion_eventos', 1);
    }

    public function test_no_se_puede_inscribir_a_un_evento_no_publicado(): void
    {
        $evento = $this->crearEvento(['publicado' => false]);

        Livewire::test(CalendarioEventos::class)
            ->call('abrirInscripcion', $evento->idEvento)
            ->assertSet('mostrarModalInscripcion', false)
            ->assertDispatched('show-toast', type: 'error');

        $this->assertDatabaseCount('inscripcion_eventos', 0);
    }

    public function test_la_inscripcion_requiere_nombre_y_email_validos(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);

        Livewire::test(CalendarioEventos::class)
            ->call('abrirInscripcion', $evento->idEvento)
            ->set('nombre', '')
            ->set('email', 'correo-invalido')
            ->call('confirmarInscripcion')
            ->assertHasErrors(['nombre' => 'required', 'email' => 'email']);

        $this->assertDatabaseCount('inscripcion_eventos', 0);
    }

    public function test_estudiante_puede_ver_sus_eventos_inscritos(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($user);

        $component = Livewire::test(MisEventos::class)
            ->assertSet('tab', 'proximos');

        // Acceder al método computado para verificar los eventos del estudiante
        $inscripciones = $component->instance()->misInscripciones;

        $this->assertSame(1, $inscripciones->total());
        $this->assertSame($evento->idEvento, $inscripciones->first()->idEvento);
    }

    public function test_estudiante_puede_cancelar_su_inscripcion_y_se_libera_el_cupo(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'inscritos_actual' => 1]);
        $user = $this->crearEstudianteUser();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($user);

        Livewire::test(MisEventos::class)
            ->call('cancelarInscripcion', $inscripcion->idInscripcion)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idInscripcion' => $inscripcion->idInscripcion,
            'estado' => 'cancelada',
        ]);

        // El cupo debe liberarse
        $this->assertDatabaseHas('eventos', [
            'idEvento' => $evento->idEvento,
            'inscritos_actual' => 0,
        ]);
    }

    public function test_estudiante_no_puede_cancelar_inscripcion_de_otro_usuario(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $userA = $this->crearEstudianteUser();
        $userB = $this->crearEstudianteUser();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $userA->id,
            'nombre' => $userA->name,
            'email' => $userA->email,
            'estado' => 'confirmada',
        ]);

        $this->actingAs($userB);

        Livewire::test(MisEventos::class)
            ->call('cancelarInscripcion', $inscripcion->idInscripcion)
            ->assertDispatched('show-toast', type: 'error');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idInscripcion' => $inscripcion->idInscripcion,
            'estado' => 'confirmada',
        ]);
    }

    public function test_admin_puede_marcar_el_pago_de_una_inscripcion_como_realizado(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $user = $this->crearEstudianteUser();
        $admin = $this->crearAdmin();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $this->actingAs($admin);

        Livewire::test(InscripcionesEvento::class, ['evento' => $evento])
            ->call('togglePago', $inscripcion->idInscripcion)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idInscripcion' => $inscripcion->idInscripcion,
            'pago_realizado' => true,
        ]);
    }

    public function test_admin_puede_marcar_el_pago_de_una_inscripcion_como_pendiente(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $user = $this->crearEstudianteUser();
        $admin = $this->crearAdmin();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        $this->actingAs($admin);

        Livewire::test(InscripcionesEvento::class, ['evento' => $evento])
            ->call('togglePago', $inscripcion->idInscripcion)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idInscripcion' => $inscripcion->idInscripcion,
            'pago_realizado' => false,
        ]);
    }

    public function test_estudiante_no_puede_marcar_el_pago_de_una_inscripcion(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);
        $user = $this->crearEstudianteUser();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $this->actingAs($user);

        Livewire::test(InscripcionesEvento::class, ['evento' => $evento])
            ->call('togglePago', $inscripcion->idInscripcion)
            ->assertForbidden();

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idInscripcion' => $inscripcion->idInscripcion,
            'pago_realizado' => false,
        ]);
    }

    public function test_total_recaudado_suma_el_valor_de_los_pagos_confirmados(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $admin = $this->crearAdmin();

        // Dos inscripciones pagadas y una pendiente
        $user1 = $this->crearEstudianteUser();
        $user2 = $this->crearEstudianteUser();
        $user3 = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user1->id,
            'nombre' => $user1->name,
            'email' => $user1->email,
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user2->id,
            'nombre' => $user2->name,
            'email' => $user2->email,
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user3->id,
            'nombre' => $user3->name,
            'email' => $user3->email,
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $this->actingAs($admin);

        // 2 pagadas * $50.000 = $100.000
        Livewire::test(InscripcionesEvento::class, ['evento' => $evento])
            ->assertViewHas('pagados', 2)
            ->assertViewHas('pendientes', 1)
            ->assertViewHas('totalRecaudado', 100000.0);
    }

    public function test_total_recaudado_se_actualiza_al_marcar_un_pago_como_confirmado(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $admin = $this->crearAdmin();
        $user = $this->crearEstudianteUser();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $this->actingAs($admin);

        // Antes de marcar el pago, el total es 0
        Livewire::test(InscripcionesEvento::class, ['evento' => $evento])
            ->assertViewHas('pagados', 0)
            ->assertViewHas('totalRecaudado', 0.0)
            ->call('togglePago', $inscripcion->idInscripcion)
            // Después de marcar el pago, el total refleja el costo del evento
            ->assertViewHas('pagados', 1)
            ->assertViewHas('totalRecaudado', 50000.0);
    }

    public function test_gestion_eventos_muestra_el_total_de_ingresos_de_todos_los_eventos(): void
    {
        $admin = $this->crearAdmin();

        // Evento 1 con costo 30000 y una inscripción pagada
        $evento1 = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 30000]);
        $user1 = $this->crearEstudianteUser();
        InscripcionEvento::create([
            'idEvento' => $evento1->idEvento,
            'user_id' => $user1->id,
            'nombre' => $user1->name,
            'email' => $user1->email,
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        // Evento 2 con costo 20000 y una inscripción pendiente (no suma)
        $evento2 = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 20000]);
        $user2 = $this->crearEstudianteUser();
        InscripcionEvento::create([
            'idEvento' => $evento2->idEvento,
            'user_id' => $user2->id,
            'nombre' => $user2->name,
            'email' => $user2->email,
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        // Evento 3 con costo 50000 y una inscripción cancelada (no suma)
        $evento3 = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user3 = $this->crearEstudianteUser();
        InscripcionEvento::create([
            'idEvento' => $evento3->idEvento,
            'user_id' => $user3->id,
            'nombre' => $user3->name,
            'email' => $user3->email,
            'estado' => 'cancelada',
            'pago_realizado' => true,
        ]);

        $this->actingAs($admin);

        // Métricas globales:
        // - 3 eventos creados
        // - 2 inscripciones confirmadas (evento 1 pagada + evento 2 pendiente)
        // - 1 pago pendiente (evento 2)
        // - Solo suma la inscripción confirmada y pagada del evento 1: $30.000
        Livewire::test(IndexEventos::class)
            ->assertViewHas('totalEventos', 3)
            ->assertViewHas('totalInscritos', 2)
            ->assertViewHas('pagosPendientes', 1)
            ->assertViewHas('totalIngresos', 30000.0);
    }

    public function test_visitante_puede_ver_el_detalle_de_un_evento_publicado(): void
    {
        $evento = $this->crearEvento(['titulo' => 'Conferencia de Tecnología']);

        $this->get(route('eventos.show', $evento))
            ->assertOk()
            ->assertSee('Conferencia de Tecnología');
    }

    public function test_evento_no_publicado_devuelve_404_en_el_detalle(): void
    {
        $evento = $this->crearEvento(['publicado' => false]);

        $this->get(route('eventos.show', $evento))
            ->assertNotFound();
    }

    public function test_visitante_puede_inscribirse_desde_el_detalle_del_evento(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10]);

        Livewire::test(EventoDetalle::class, ['evento' => $evento])
            ->call('abrirInscripcion')
            ->assertSet('mostrarModalInscripcion', true)
            ->set('nombre', 'María')
            ->set('apellido', 'Gómez')
            ->set('email', 'maria@example.com')
            ->call('confirmarInscripcion')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('inscripcion_eventos', [
            'idEvento' => $evento->idEvento,
            'email' => 'maria@example.com',
            'estado' => 'confirmada',
        ]);

        $this->assertSame(1, $evento->fresh()->inscritos_actual);
    }

    public function test_no_se_puede_inscribir_si_los_cupos_estan_agotados_desde_el_detalle(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 1, 'inscritos_actual' => 1]);

        Livewire::test(EventoDetalle::class, ['evento' => $evento])
            ->call('abrirInscripcion')
            ->assertSet('mostrarModalInscripcion', false)
            ->assertDispatched('show-toast', type: 'warning');
    }

    public function test_admin_puede_ver_el_detalle_de_un_evento(): void
    {
        $evento = $this->crearEvento(['titulo' => 'Taller de Robótica']);
        $admin = $this->crearAdmin();

        $this->actingAs($admin)
            ->get(route('admin.eventos.show', $evento))
            ->assertOk()
            ->assertSee('Taller de Robótica');
    }

    public function test_visitante_no_puede_ver_el_detalle_admin_de_un_evento(): void
    {
        $evento = $this->crearEvento();

        $this->get(route('admin.eventos.show', $evento))
            ->assertRedirect(route('login'));
    }

    public function test_admin_puede_alternar_publicacion_desde_el_detalle(): void
    {
        $evento = $this->crearEvento(['publicado' => true]);
        $admin = $this->crearAdmin();

        $this->actingAs($admin);

        Livewire::test(MostrarEvento::class, ['evento' => $evento])
            ->call('togglePublicado');

        $this->assertFalse($evento->fresh()->publicado);
    }

    public function test_admin_puede_ver_la_vista_de_pagos_pendientes_de_eventos(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Carlos',
            'email' => 'carlos@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $admin = $this->crearAdmin();

        $this->actingAs($admin)
            ->get(route('admin.eventos.pagos-pendientes'))
            ->assertOk()
            ->assertSee('Pagos Pendientes de Eventos')
            ->assertSee('carlos@example.com');
    }

    public function test_visitante_no_puede_ver_los_pagos_pendientes_de_eventos(): void
    {
        $this->get(route('admin.eventos.pagos-pendientes'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_puede_marcar_un_pago_pendiente_como_pagado(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user = $this->crearEstudianteUser();

        $inscripcion = InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Carlos',
            'email' => 'carlos@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $admin = $this->crearAdmin();
        $this->actingAs($admin);

        Livewire::test(PagosPendientesEventos::class)
            ->call('togglePago', $inscripcion->idInscripcion)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertTrue($inscripcion->fresh()->pago_realizado);
    }

    public function test_admin_puede_ver_la_vista_de_personas_inscritas(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Maria',
            'email' => 'maria@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        $admin = $this->crearAdmin();

        $this->actingAs($admin)
            ->get(route('admin.eventos.inscritos'))
            ->assertOk()
            ->assertSee('Personas Inscritas en Eventos')
            ->assertSee('maria@example.com');
    }

    public function test_visitante_no_puede_ver_las_personas_inscritas(): void
    {
        $this->get(route('admin.eventos.inscritos'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_puede_ver_la_vista_de_ingresos_por_eventos(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Pedro',
            'email' => 'pedro@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        $admin = $this->crearAdmin();

        $this->actingAs($admin)
            ->get(route('admin.eventos.ingresos'))
            ->assertOk()
            ->assertSee('Ingresos por Eventos')
            ->assertSee('pedro@example.com');
    }

    public function test_visitante_no_puede_ver_los_ingresos_por_eventos(): void
    {
        $this->get(route('admin.eventos.ingresos'))
            ->assertRedirect(route('login'));
    }

    public function test_vista_de_inscritos_solo_muestra_inscripciones_confirmadas(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Confirmada',
            'email' => 'confirmada@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Cancelada',
            'email' => 'cancelada@example.com',
            'estado' => 'cancelada',
            'pago_realizado' => false,
        ]);

        $admin = $this->crearAdmin();
        $this->actingAs($admin);

        Livewire::test(InscritosEventos::class)
            ->assertSee('confirmada@example.com')
            ->assertDontSee('cancelada@example.com');
    }

    public function test_vista_de_ingresos_solo_muestra_pagos_realizados(): void
    {
        $evento = $this->crearEvento(['cupo_maximo' => 10, 'costo' => 50000]);
        $user = $this->crearEstudianteUser();

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'Pagado',
            'email' => 'pagado@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => true,
        ]);

        InscripcionEvento::create([
            'idEvento' => $evento->idEvento,
            'user_id' => $user->id,
            'nombre' => 'SinPagar',
            'email' => 'sinpagar@example.com',
            'estado' => 'confirmada',
            'pago_realizado' => false,
        ]);

        $admin = $this->crearAdmin();
        $this->actingAs($admin);

        Livewire::test(IngresosEventos::class)
            ->assertSee('pagado@example.com')
            ->assertDontSee('sinpagar@example.com');
    }
}
