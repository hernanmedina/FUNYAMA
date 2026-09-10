<?php

namespace Tests\Feature;

use App\Livewire\Admin\Cursos\EditarCurso;
use App\Models\Administrador;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditarCursoTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): User
    {
        $user = User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);

        Administrador::create([
            'user_id' => $user->id,
            'departamento' => 'General',
            'cargo' => 'Administrador',
            'super_admin' => true,
        ]);

        return $user;
    }

    private function crearCurso(array $overrides = []): Curso
    {
        return Curso::create(array_merge([
            'codigo' => 'CUR-'.now()->year.'-001',
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'descripcion' => 'Descripción del curso de prueba para validar la edición.',
            'cronograma' => 'Lunes y miércoles de 6 a 8 pm.',
            'requisitos' => 'Requisitos del curso de prueba.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ], $overrides));
    }

    public function test_carga_los_datos_del_curso_desde_la_base_de_datos(): void
    {
        $admin = $this->crearAdmin();
        $curso = $this->crearCurso([
            'cronograma' => 'lunes, martes y viernes hora 10 am a 12 pm',
            'requisitos' => 'Conocimientos básicos de contabilidad.',
        ]);

        Livewire::actingAs($admin)
            ->test(EditarCurso::class, ['curso' => $curso])
            ->assertSet('cronograma', 'lunes, martes y viernes hora 10 am a 12 pm')
            ->assertSet('requisitos', 'Conocimientos básicos de contabilidad.');
    }

    public function test_permite_actualizar_sin_requisitos(): void
    {
        $admin = $this->crearAdmin();
        $curso = $this->crearCurso();

        Livewire::actingAs($admin)
            ->test(EditarCurso::class, ['curso' => $curso])
            ->set('requisitos', '')
            ->call('actualizarCurso')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.cursos.index'));

        $this->assertSame('', $curso->fresh()->requisitos);
        $this->assertSame('¡Curso actualizado exitosamente!', session('success'));
    }

    public function test_formatea_la_fecha_de_inicio_para_el_input_date(): void
    {
        $admin = $this->crearAdmin();
        $curso = $this->crearCurso(['fecha_inicio' => '2026-09-24']);

        Livewire::actingAs($admin)
            ->test(EditarCurso::class, ['curso' => $curso])
            ->assertSet('fecha_inicio', '2026-09-24');
    }
}
