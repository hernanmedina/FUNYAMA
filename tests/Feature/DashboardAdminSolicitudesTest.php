<?php

namespace Tests\Feature;

use App\Livewire\Admin\DashboardAdmin;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardAdminSolicitudesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_only_shows_pending_inscription_requests_in_the_quick_list(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);

        $inscriptionRequest = Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de prueba de inscripción',
            'mensaje' => 'Mensaje de prueba',
            'email_contacto' => 'estudiante@example.com',
            'estado' => 'pendiente',
            'user_id' => $admin->id,
        ]);

        $otherRequest = Solicitud::create([
            'tipo' => 'problema',
            'asunto' => 'Solicitud de problema',
            'mensaje' => 'Mensaje de otra solicitud',
            'email_contacto' => 'otro@example.com',
            'estado' => 'pendiente',
            'user_id' => $admin->id,
        ]);

        $this->actingAs($admin);

        Livewire::test(DashboardAdmin::class)
            ->assertSee($inscriptionRequest->asunto)
            ->assertDontSee($otherRequest->asunto);
    }

    public function test_dashboard_income_statistics_only_count_verified_paid_enrollments(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);

        $cursoPagado = Curso::create([
            'codigo' => 'CURSO-PAID-001',
            'nombre' => 'Curso Pagado',
            'slug' => 'curso-pagado',
            'descripcion' => 'Curso de prueba con costo.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'objetivos' => 'Objetivos de prueba',
            'materiales_incluidos' => 'Materiales de prueba',
            'cupo_total' => 40,
            'cupo_disponible' => 40,
            'duracion_horas' => 10,
            'duracion_texto' => '10 horas',
            'precio_regular' => 120,
            'precio_descuento' => null,
            'nivel' => 'principiante',
            'publicado' => true,
            'destacado' => false,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(7)->toDateString(),
        ]);

        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Prueba',
            'role' => 'estu',
        ]);

        $estudiante = Estudiante::create([
            'codigo' => 'EST-TEST-001',
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        $estudiante->cursos()->attach($cursoPagado->codigo, [
            'estado' => 'inscrito',
            'pago_realizado' => 120,
            'estado_pago' => 'completo',
            'fecha_inscripcion' => now(),
            'progreso' => 0,
        ]);

        $this->actingAs($admin);

        Livewire::test(DashboardAdmin::class)
            ->assertSet('estadisticas.ingresos_totales', 120.0)
            ->assertSet('estadisticas.ingresos_recientes', 120.0)
            ->assertSet('estadisticas.matriculas_gratuitas', 0)
            ->assertSet('estadisticas.total_eventos_publicados', 0);
    }

    public function test_dashboard_income_statistics_fallback_to_course_price_when_payment_amount_is_missing(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);

        $cursoPagado = Curso::create([
            'codigo' => 'CURSO-PAID-002',
            'nombre' => 'Curso Pagado sin monto en pivot',
            'slug' => 'curso-pagado-sin-monto-en-pivot',
            'descripcion' => 'Curso de prueba con costo.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'objetivos' => 'Objetivos de prueba',
            'materiales_incluidos' => 'Materiales de prueba',
            'cupo_total' => 40,
            'cupo_disponible' => 40,
            'duracion_horas' => 10,
            'duracion_texto' => '10 horas',
            'precio_regular' => 199,
            'precio_descuento' => null,
            'nivel' => 'principiante',
            'publicado' => true,
            'destacado' => false,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(7)->toDateString(),
        ]);

        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'SinMonto',
            'role' => 'estu',
        ]);

        $estudiante = Estudiante::create([
            'codigo' => 'EST-TEST-002',
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        $estudiante->cursos()->attach($cursoPagado->codigo, [
            'estado' => 'inscrito',
            'pago_realizado' => 0,
            'estado_pago' => 'completo',
            'fecha_inscripcion' => now(),
            'progreso' => 0,
        ]);

        $this->actingAs($admin);

        Livewire::test(DashboardAdmin::class)
            ->assertSet('estadisticas.ingresos_totales', 199.0)
            ->assertSet('estadisticas.ingresos_recientes', 199.0)
            ->assertSet('estadisticas.matriculas_gratuitas', 0)
            ->assertSet('estadisticas.total_eventos_publicados', 0);
    }

    public function test_admin_can_change_a_payment_status_from_the_dashboard_control_panel(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);

        $cursoPagado = Curso::create([
            'codigo' => 'CURSO-PAID-003',
            'nombre' => 'Curso para cambiar estado',
            'slug' => 'curso-para-cambiar-estado',
            'descripcion' => 'Curso de prueba para manejo manual de pagos.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'objetivos' => 'Objetivos de prueba',
            'materiales_incluidos' => 'Materiales de prueba',
            'cupo_total' => 40,
            'cupo_disponible' => 40,
            'duracion_horas' => 10,
            'duracion_texto' => '10 horas',
            'precio_regular' => 250,
            'precio_descuento' => null,
            'nivel' => 'principiante',
            'publicado' => true,
            'destacado' => false,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(7)->toDateString(),
        ]);

        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Pagar',
            'role' => 'estu',
        ]);

        $estudiante = Estudiante::create([
            'codigo' => 'EST-TEST-003',
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        $estudiante->cursos()->attach($cursoPagado->codigo, [
            'estado' => 'inscrito',
            'pago_realizado' => 0,
            'estado_pago' => 'pendiente',
            'fecha_inscripcion' => now(),
            'progreso' => 0,
        ]);

        $this->actingAs($admin);

        Livewire::test(DashboardAdmin::class)
            ->call('actualizarEstadoPago', $cursoPagado->codigo, $estudiante->codigo, 'completo')
            ->assertSet('estadisticas.ingresos_totales', 250.0)
            ->assertSet('estadisticas.ingresos_recientes', 250.0)
            ->assertSet('estadisticas.matriculas_gratuitas', 0)
            ->assertSet('estadisticas.total_eventos_publicados', 0)
            ->assertSee('Control de Pagos');

        $this->assertDatabaseHas('curso_estudiante', [
            'curso_id' => $cursoPagado->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado_pago' => 'completo',
            'pago_realizado' => 250,
        ]);
    }

    public function test_admin_can_export_a_dashboard_report_from_the_quick_actions_modal(): void
    {
        $admin = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Export',
            'role' => 'admin',
        ]);

        Curso::create([
            'codigo' => 'CURSO-EXPORT-001',
            'nombre' => 'Curso Gratuito',
            'slug' => 'curso-gratuito',
            'descripcion' => 'Curso gratuito para reporte.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'objetivos' => 'Objetivos de prueba',
            'materiales_incluidos' => 'Materiales de prueba',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'duracion_horas' => 5,
            'duracion_texto' => '5 horas',
            'precio_regular' => 0,
            'precio_descuento' => 0,
            'nivel' => 'principiante',
            'publicado' => true,
            'destacado' => false,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(5)->toDateString(),
        ]);

        $this->actingAs($admin);

        Livewire::test(DashboardAdmin::class)
            ->set('tipoReporte', 'cursos')
            ->set('subtipoReporte', 'gratuitos')
            ->set('formatoReporte', 'excel')
            ->call('exportarReporte')
            ->assertFileDownloaded('reporte-cursos-gratuitos.xlsx');
    }
}
