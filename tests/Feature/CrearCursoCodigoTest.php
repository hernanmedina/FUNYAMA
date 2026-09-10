<?php

namespace Tests\Feature;

use App\Livewire\Admin\Cursos\CrearCurso;
use App\Livewire\Admin\Cursos\IndexCursos;
use App\Models\Administrador;
use App\Models\Curso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CrearCursoCodigoTest extends TestCase
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

    private function crearCurso(string $codigo): Curso
    {
        return Curso::create([
            'codigo' => $codigo,
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba-'.strtolower($codigo),
            'descripcion' => 'Descripción del curso de prueba para validar la generación del código.',
            'cronograma' => 'Cronograma del curso de prueba.',
            'requisitos' => 'Requisitos del curso de prueba.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);
    }

    public function test_genera_el_primer_codigo_cuando_no_existen_cursos(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(CrearCurso::class)
            ->assertSet('codigo', 'CUR-'.now()->year.'-001');
    }

    public function test_genera_el_siguiente_codigo_secuencial(): void
    {
        $admin = $this->crearAdmin();
        $anio = now()->year;

        $this->crearCurso('CUR-'.$anio.'-001');
        $this->crearCurso('CUR-'.$anio.'-002');

        Livewire::actingAs($admin)
            ->test(CrearCurso::class)
            ->assertSet('codigo', 'CUR-'.$anio.'-003');
    }

    public function test_ignora_cursos_de_otros_anios(): void
    {
        $admin = $this->crearAdmin();
        $anio = now()->year;

        $this->crearCurso('CUR-'.($anio - 1).'-099');

        Livewire::actingAs($admin)
            ->test(CrearCurso::class)
            ->assertSet('codigo', 'CUR-'.$anio.'-001');
    }

    public function test_regenerar_codigo_actualiza_el_valor(): void
    {
        $admin = $this->crearAdmin();
        $anio = now()->year;

        $this->crearCurso('CUR-'.$anio.'-001');

        Livewire::actingAs($admin)
            ->test(CrearCurso::class)
            ->set('codigo', 'CUR-'.$anio.'-999')
            ->call('regenerarCodigo')
            ->assertSet('codigo', 'CUR-'.$anio.'-002');
    }

    public function test_al_crear_un_curso_redirige_al_indice_con_mensaje_de_exito(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(CrearCurso::class)
            ->set('nombre', 'Curso Nuevo')
            ->set('slug', 'curso-nuevo')
            ->set('descripcion', 'Descripción suficientemente larga del curso nuevo.')
            ->set('cronograma', 'Lunes y miércoles de 6 a 8 pm.')
            ->set('cupo_total', 20)
            ->set('precio_regular', 100)
            ->call('store')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.cursos.index'));

        $this->assertDatabaseHas('cursos', ['nombre' => 'Curso Nuevo']);
        $this->assertSame('¡Curso creado exitosamente!', session('success'));
    }

    public function test_el_indice_despacha_el_toast_al_volver_de_crear_un_curso(): void
    {
        $admin = $this->crearAdmin();

        session()->put('success', '¡Curso creado exitosamente!');

        Livewire::actingAs($admin)
            ->test(IndexCursos::class)
            ->assertDispatched('show-toast', type: 'success', message: '¡Curso creado exitosamente!');
    }
}
