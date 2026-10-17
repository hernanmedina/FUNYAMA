<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Docente;
use App\Models\User;
use App\Notifications\InvitacionUsuario;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class GestionUsuariosTest extends TestCase
{
    use RefreshDatabase;

    private function crearSuperAdmin(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        Administrador::create([
            'user_id' => $user->id,
            'super_admin' => true,
        ]);

        return $user->fresh();
    }

    private function crearAdminSimple(): User
    {
        $user = User::factory()->create(['role' => 'admin']);
        Administrador::create([
            'user_id' => $user->id,
            'super_admin' => false,
        ]);

        return $user->fresh();
    }

    public function test_super_admin_puede_ver_el_listado_de_usuarios(): void
    {
        $superAdmin = $this->crearSuperAdmin();

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.index-usuarios')
            ->assertStatus(200);
    }

    public function test_admin_simple_no_puede_crear_usuarios(): void
    {
        $admin = $this->crearAdminSimple();

        Livewire::actingAs($admin)
            ->test('admin.usuarios.crear-usuario')
            ->set('rol', 'instructor')
            ->set('name', 'Nuevo')
            ->set('apellido', 'Apellido')
            ->set('email', 'nuevo@example.com')
            ->set('documento_ID', '11111111')
            ->set('telefono', '3000000000')
            ->set('direccion', 'Calle 1 #2-3')
            ->call('store')
            ->assertForbidden();
    }

    public function test_super_admin_puede_crear_un_administrador(): void
    {
        Notification::fake();

        $superAdmin = $this->crearSuperAdmin();

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.crear-usuario')
            ->set('rol', 'admin')
            ->set('name', 'Ana')
            ->set('apellido', 'Perez')
            ->set('email', 'ana.admin@example.com')
            ->set('documento_ID', '12345678')
            ->set('telefono', '3001234567')
            ->set('direccion', 'Calle 10 #20-30')
            ->set('departamento', 'Académico')
            ->set('cargo', 'Coordinadora')
            ->set('super_admin', false)
            ->call('store')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'ana.admin@example.com',
            'role' => 'admin',
        ]);

        $user = User::where('email', 'ana.admin@example.com')->first();
        $this->assertDatabaseHas('administradores', [
            'user_id' => $user->id,
            'departamento' => 'Académico',
            'cargo' => 'Coordinadora',
        ]);

        Notification::assertSentTo($user, InvitacionUsuario::class);
    }

    public function test_super_admin_puede_crear_un_docente_con_datos_extra(): void
    {
        Notification::fake();

        $superAdmin = $this->crearSuperAdmin();

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.crear-usuario')
            ->set('rol', 'instructor')
            ->set('name', 'Carlos')
            ->set('apellido', 'Gomez')
            ->set('email', 'carlos.docente@example.com')
            ->set('documento_ID', '87654321')
            ->set('telefono', '3009876543')
            ->set('direccion', 'Carrera 5 #10-15')
            ->set('especialidad', 'Matemáticas')
            ->set('biografia', 'Docente con 10 años de experiencia.')
            ->set('telefono_contacto', '3001234567')
            ->set('activo', true)
            ->call('store')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseHas('users', [
            'email' => 'carlos.docente@example.com',
            'role' => 'instructor',
        ]);

        $user = User::where('email', 'carlos.docente@example.com')->first();
        $this->assertDatabaseHas('docentes', [
            'user_id' => $user->id,
            'especialidad' => 'Matemáticas',
            'telefono_contacto' => '3001234567',
        ]);

        Notification::assertSentTo($user, InvitacionUsuario::class);
    }

    public function test_no_se_puede_crear_usuario_con_email_duplicado(): void
    {
        $superAdmin = $this->crearSuperAdmin();
        User::factory()->create(['email' => 'existente@example.com']);

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.crear-usuario')
            ->set('rol', 'instructor')
            ->set('name', 'Duplicado')
            ->set('apellido', 'Apellido')
            ->set('email', 'existente@example.com')
            ->set('documento_ID', '22222222')
            ->set('telefono', '3001111111')
            ->set('direccion', 'Calle 2 #3-4')
            ->call('store')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_el_listado_solo_muestra_administradores_y_docentes(): void
    {
        $superAdmin = $this->crearSuperAdmin();

        $docente = User::factory()->create(['role' => 'instructor', 'name' => 'DocenteVisible']);
        Docente::create(['user_id' => $docente->id]);

        User::factory()->create(['role' => 'estu', 'name' => 'EstudianteOculto']);

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.index-usuarios')
            ->assertSee('DocenteVisible')
            ->assertDontSee('EstudianteOculto');
    }

    public function test_el_filtro_por_rol_funciona(): void
    {
        $superAdmin = $this->crearSuperAdmin();

        $docente = User::factory()->create(['role' => 'instructor', 'name' => 'DocenteFiltrado']);
        Docente::create(['user_id' => $docente->id]);

        $otroAdmin = User::factory()->create(['role' => 'admin', 'name' => 'AdminFiltrado']);
        Administrador::create(['user_id' => $otroAdmin->id]);

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.index-usuarios')
            ->call('setRolFiltro', 'instructor')
            ->assertSee('DocenteFiltrado')
            ->assertDontSee('AdminFiltrado');
    }

    public function test_super_admin_puede_editar_un_docente(): void
    {
        $superAdmin = $this->crearSuperAdmin();

        $docente = User::factory()->create([
            'role' => 'instructor',
            'name' => 'DocenteEditable',
            'apellido' => 'Editable',
            'documento_ID' => '33333333',
            'telefono' => '3002222222',
            'direccion' => 'Calle 3 #4-5',
        ]);
        Docente::create(['user_id' => $docente->id, 'especialidad' => 'Antigua']);

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.editar-usuario', ['usuario' => $docente])
            ->set('especialidad', 'Nueva Especialidad')
            ->call('update')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.usuarios.index'));

        $this->assertDatabaseHas('docentes', [
            'user_id' => $docente->id,
            'especialidad' => 'Nueva Especialidad',
        ]);
    }

    public function test_admin_simple_no_puede_editar_usuarios(): void
    {
        $admin = $this->crearAdminSimple();

        $docente = User::factory()->create(['role' => 'instructor']);
        Docente::create(['user_id' => $docente->id]);

        Livewire::actingAs($admin)
            ->test('admin.usuarios.editar-usuario', ['usuario' => $docente])
            ->assertForbidden();
    }

    public function test_los_datos_personales_son_requeridos_al_crear(): void
    {
        $superAdmin = $this->crearSuperAdmin();

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.crear-usuario')
            ->set('rol', 'instructor')
            ->set('name', 'Sin Datos')
            ->set('email', 'sindatos@example.com')
            ->call('store')
            ->assertHasErrors([
                'apellido' => 'required',
                'documento_ID' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
            ]);
    }

    public function test_los_datos_personales_son_requeridos_al_editar(): void
    {
        $superAdmin = $this->crearSuperAdmin();

        $docente = User::factory()->create([
            'role' => 'instructor',
            'name' => 'DocenteRequerido',
            'apellido' => 'Apellido',
            'documento_ID' => '44444444',
            'telefono' => '3003333333',
            'direccion' => 'Calle 4 #5-6',
        ]);
        Docente::create(['user_id' => $docente->id]);

        Livewire::actingAs($superAdmin)
            ->test('admin.usuarios.editar-usuario', ['usuario' => $docente])
            ->set('apellido', '')
            ->set('documento_ID', '')
            ->set('telefono', '')
            ->set('direccion', '')
            ->call('update')
            ->assertHasErrors([
                'apellido' => 'required',
                'documento_ID' => 'required',
                'telefono' => 'required',
                'direccion' => 'required',
            ]);
    }
}
