<?php

namespace Tests\Feature;

use App\Livewire\Estudiante\MisCursos;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MisCursosTest extends TestCase
{
    use RefreshDatabase;

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

    private function crearEstudiante(): Estudiante
    {
        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Test',
            'role' => 'estu',
        ]);

        return Estudiante::create([
            'codigo' => 'EST-TEST-001',
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);
    }

    private function inscribirEstudiante(Estudiante $estudiante, string $cursoCodigo, string $estadoPago): void
    {
        $estudiante->cursos()->attach($cursoCodigo, [
            'estado' => 'en_progreso',
            'estado_pago' => $estadoPago,
            'pago_realizado' => $estadoPago === 'completo' ? 100 : 0,
            'fecha_inscripcion' => now(),
            'progreso' => 50,
        ]);
    }

    public function test_estudiante_no_puede_marcar_curso_completado_si_pago_es_pendiente(): void
    {
        $curso = $this->crearCurso('CUR-PEND-001');
        $estudiante = $this->crearEstudiante();
        $this->inscribirEstudiante($estudiante, $curso->codigo, 'pendiente');

        $this->actingAs($estudiante->user);

        Livewire::test(MisCursos::class)
            ->call('marcarComoCompletado', $curso->codigo)
            ->assertDispatched('show-toast', type: 'error');

        $this->assertDatabaseHas('curso_estudiante', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado' => 'en_progreso',
        ]);
    }

    public function test_estudiante_no_puede_marcar_curso_completado_si_pago_es_parcial(): void
    {
        $curso = $this->crearCurso('CUR-PARC-001');
        $estudiante = $this->crearEstudiante();
        $this->inscribirEstudiante($estudiante, $curso->codigo, 'parcial');

        $this->actingAs($estudiante->user);

        Livewire::test(MisCursos::class)
            ->call('marcarComoCompletado', $curso->codigo)
            ->assertDispatched('show-toast', type: 'error');

        $this->assertDatabaseHas('curso_estudiante', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado' => 'en_progreso',
        ]);
    }

    public function test_estudiante_puede_marcar_curso_completado_si_pago_es_completo(): void
    {
        $curso = $this->crearCurso('CUR-COMP-001');
        $estudiante = $this->crearEstudiante();
        $this->inscribirEstudiante($estudiante, $curso->codigo, 'completo');

        $this->actingAs($estudiante->user);

        Livewire::test(MisCursos::class)
            ->call('marcarComoCompletado', $curso->codigo)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('curso_estudiante', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado' => 'completado',
        ]);
    }
}
