<?php

namespace Tests\Feature;

use App\Livewire\Admin\ControlPagos;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ControlPagosTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function crearCurso(string $codigo): Curso
    {
        return Curso::create([
            'codigo' => $codigo,
            'nombre' => 'Curso '.$codigo,
            'slug' => 'curso-'.strtolower($codigo),
            'descripcion' => 'Curso de prueba.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'objetivos' => 'Objetivos de prueba',
            'materiales_incluidos' => 'Materiales de prueba',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'duracion_horas' => 5,
            'duracion_texto' => '5 horas',
            'precio_regular' => 100,
            'precio_descuento' => null,
            'nivel' => 'principiante',
            'publicado' => true,
            'destacado' => false,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(7)->toDateString(),
        ]);
    }

    private function crearEstudiante(string $codigo, string $nombre): Estudiante
    {
        $user = User::factory()->create([
            'name' => $nombre,
            'apellido' => 'Test',
            'role' => 'estu',
        ]);

        return Estudiante::create([
            'codigo' => $codigo,
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);
    }

    private function inscribir(Estudiante $estudiante, string $cursoCodigo, string $estadoCurso, string $estadoPago = 'completo'): void
    {
        $estudiante->cursos()->attach($cursoCodigo, [
            'estado' => $estadoCurso,
            'estado_pago' => $estadoPago,
            'pago_realizado' => $estadoPago === 'completo' ? 100 : 0,
            'fecha_inscripcion' => now(),
            'progreso' => $estadoCurso === 'completado' ? 100 : 50,
        ]);
    }

    public function test_filtro_finalizado_muestra_solo_cursos_completados(): void
    {
        $curso = $this->crearCurso('CUR-FIN-001');
        $finalizado = $this->crearEstudiante('EST-FIN-001', 'AlumnoTerminado');
        $enCurso = $this->crearEstudiante('EST-CUR-001', 'AlumnoCursando');

        $this->inscribir($finalizado, $curso->codigo, 'completado');
        $this->inscribir($enCurso, $curso->codigo, 'en_progreso');

        $this->actingAs($this->crearAdmin());

        Livewire::test(ControlPagos::class)
            ->call('setEstadoCursoFilter', 'finalizado')
            ->assertSet('estadoCursoFilter', 'finalizado')
            ->assertSee('AlumnoTerminado')
            ->assertDontSee('AlumnoCursando');
    }

    public function test_filtro_en_curso_excluye_los_completados(): void
    {
        $curso = $this->crearCurso('CUR-ENC-001');
        $finalizado = $this->crearEstudiante('EST-FIN-002', 'AlumnoTerminado');
        $enCurso = $this->crearEstudiante('EST-CUR-002', 'AlumnoCursando');

        $this->inscribir($finalizado, $curso->codigo, 'completado');
        $this->inscribir($enCurso, $curso->codigo, 'en_progreso');

        $this->actingAs($this->crearAdmin());

        Livewire::test(ControlPagos::class)
            ->call('setEstadoCursoFilter', 'en_curso')
            ->assertSet('estadoCursoFilter', 'en_curso')
            ->assertSee('AlumnoCursando')
            ->assertDontSee('AlumnoTerminado');
    }

    public function test_filtro_estado_curso_se_puede_limpiar(): void
    {
        $curso = $this->crearCurso('CUR-LIM-001');
        $finalizado = $this->crearEstudiante('EST-FIN-003', 'AlumnoTerminado');
        $enCurso = $this->crearEstudiante('EST-CUR-003', 'AlumnoCursando');

        $this->inscribir($finalizado, $curso->codigo, 'completado');
        $this->inscribir($enCurso, $curso->codigo, 'en_progreso');

        $this->actingAs($this->crearAdmin());

        Livewire::test(ControlPagos::class)
            ->call('setEstadoCursoFilter', 'finalizado')
            ->assertSet('estadoCursoFilter', 'finalizado')
            ->call('setEstadoCursoFilter', 'finalizado')
            ->assertSet('estadoCursoFilter', null)
            ->assertSee('AlumnoTerminado')
            ->assertSee('AlumnoCursando');
    }

    public function test_filtro_estado_curso_combina_con_estado_pago(): void
    {
        $curso = $this->crearCurso('CUR-COM-001');
        $finalizadoPagado = $this->crearEstudiante('EST-FP-001', 'AlumnoPagado');
        $finalizadoPendiente = $this->crearEstudiante('EST-FP-002', 'AlumnoMoroso');

        $this->inscribir($finalizadoPagado, $curso->codigo, 'completado', 'completo');
        $this->inscribir($finalizadoPendiente, $curso->codigo, 'completado', 'pendiente');

        $this->actingAs($this->crearAdmin());

        Livewire::test(ControlPagos::class)
            ->call('setEstadoCursoFilter', 'finalizado')
            ->call('setEstadoPagoFilter', 'pendiente')
            ->assertSee('AlumnoMoroso')
            ->assertDontSee('AlumnoPagado');
    }
}
