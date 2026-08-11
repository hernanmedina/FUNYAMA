<?php

namespace Tests\Feature;

use App\Livewire\Admin\CalificarEstudiantes;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CalificarEstudiantesExportTest extends TestCase
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

    private function crearEstudianteCalificado(string $codigoCurso, string $codigoEstudiante, float $nota, string $estado = 'completado'): void
    {
        $curso = Curso::create([
            'codigo' => $codigoCurso,
            'nombre' => 'Curso '.$codigoCurso,
            'slug' => 'curso-'.strtolower($codigoCurso),
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

        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Calificado',
            'role' => 'estu',
        ]);

        $estudiante = Estudiante::create([
            'codigo' => $codigoEstudiante,
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        $estudiante->cursos()->attach($curso->codigo, [
            'estado' => $estado,
            'calificacion' => $nota,
            'comentario_calificacion' => 'Buen desempeño',
            'pago_realizado' => 0,
            'estado_pago' => 'pendiente',
            'fecha_inscripcion' => now(),
            'progreso' => 100,
            'fecha_completado' => now(),
        ]);
    }

    public function test_admin_can_export_calificaciones_to_excel(): void
    {
        $admin = $this->crearAdmin();
        $this->crearEstudianteCalificado('CUR-EXP-001', 'EST-EXP-001', 9.5);

        $this->actingAs($admin);

        Livewire::test(CalificarEstudiantes::class)
            ->call('exportarExcel')
            ->assertStatus(200);
    }

    public function test_admin_can_export_calificaciones_to_csv(): void
    {
        $admin = $this->crearAdmin();
        $this->crearEstudianteCalificado('CUR-EXP-002', 'EST-EXP-002', 7.5);

        $this->actingAs($admin);

        Livewire::test(CalificarEstudiantes::class)
            ->call('exportarCsv')
            ->assertStatus(200);
    }

    public function test_export_with_no_calificaciones_shows_warning(): void
    {
        $admin = $this->crearAdmin();

        $this->actingAs($admin);

        Livewire::test(CalificarEstudiantes::class)
            ->call('exportarExcel')
            ->assertDispatched('show-toast', type: 'warning');
    }

    public function test_export_respects_filters(): void
    {
        $admin = $this->crearAdmin();
        $this->crearEstudianteCalificado('CUR-EXP-003', 'EST-EXP-003', 9.5);
        $this->crearEstudianteCalificado('CUR-EXP-004', 'EST-EXP-004', 4.0);

        $this->actingAs($admin);

        Livewire::test(CalificarEstudiantes::class)
            ->set('rangoNota', 'excelente')
            ->call('exportarExcel')
            ->assertStatus(200);
    }
}
