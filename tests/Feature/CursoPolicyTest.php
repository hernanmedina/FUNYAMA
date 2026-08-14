<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoPolicyTest extends TestCase
{
    use RefreshDatabase;

    private function crearCurso(): Curso
    {
        return Curso::create([
            'codigo' => 'CUR-'.fake()->unique()->numerify('####'),
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'descripcion' => 'Descripción del curso de prueba para validar la política de autorización.',
            'cronograma' => 'Cronograma del curso de prueba.',
            'requisitos' => 'Requisitos del curso de prueba.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);
    }

    public function test_un_admin_puede_actualizar_un_curso(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $curso = $this->crearCurso();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('update', $curso));
    }

    public function test_un_estudiante_no_puede_actualizar_un_curso(): void
    {
        $estudiante = User::factory()->create(['role' => 'estu']);
        $curso = $this->crearCurso();

        $this->actingAs($estudiante);

        $this->assertFalse($estudiante->can('update', $curso));
    }

    public function test_un_instructor_no_puede_actualizar_un_curso(): void
    {
        $instructor = User::factory()->create(['role' => 'instructor']);
        $curso = $this->crearCurso();

        $this->actingAs($instructor);

        $this->assertFalse($instructor->can('update', $curso));
    }

    public function test_un_admin_puede_crear_un_curso(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $this->assertTrue($admin->can('create', Curso::class));
    }

    public function test_un_estudiante_no_puede_crear_un_curso(): void
    {
        $estudiante = User::factory()->create(['role' => 'estu']);

        $this->actingAs($estudiante);

        $this->assertFalse($estudiante->can('create', Curso::class));
    }

    public function test_un_admin_puede_eliminar_un_curso(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $curso = $this->crearCurso();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('delete', $curso));
    }

    public function test_un_estudiante_no_puede_eliminar_un_curso(): void
    {
        $estudiante = User::factory()->create(['role' => 'estu']);
        $curso = $this->crearCurso();

        $this->actingAs($estudiante);

        $this->assertFalse($estudiante->can('delete', $curso));
    }
}
