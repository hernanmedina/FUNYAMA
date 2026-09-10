<?php

namespace Tests\Feature;

use App\Livewire\Estudiante\Estudiantes;
use App\Livewire\Estudiante\MostrarEstudiante;
use App\Models\Administrador;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GestionEstudiantesTest extends TestCase
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

    private function crearEstudiante(): Estudiante
    {
        $user = User::factory()->create([
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'documento_ID' => 'V-12345678',
            'email' => 'juan.perez@example.com',
            'role' => 'estu',
        ]);

        return Estudiante::create([
            'codigo' => 'EST-001',
            'user_id' => $user->id,
        ]);
    }

    public function test_la_lista_de_estudiantes_muestra_la_cedula(): void
    {
        $admin = $this->crearAdmin();
        $this->crearEstudiante();

        Livewire::actingAs($admin)
            ->test(Estudiantes::class)
            ->assertSee('V-12345678');
    }

    public function test_la_lista_de_estudiantes_enlaza_a_la_vista_del_estudiante(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();

        Livewire::actingAs($admin)
            ->test(Estudiantes::class)
            ->assertSee(route('admin.estudiantes.show', $estudiante->codigo));
    }

    public function test_admin_puede_ver_la_vista_particular_del_estudiante(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();

        $this->actingAs($admin)
            ->get(route('admin.estudiantes.show', $estudiante->codigo))
            ->assertOk()
            ->assertSee('Juan')
            ->assertSee('V-12345678');
    }

    public function test_la_vista_del_estudiante_muestra_la_cedula(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();

        Livewire::actingAs($admin)
            ->test(MostrarEstudiante::class, ['estudiante' => $estudiante])
            ->assertSee('Cédula')
            ->assertSee('V-12345678');
    }
}
