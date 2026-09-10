<?php

namespace Tests\Feature;

use App\Livewire\Admin\PersonalizarSitio;
use App\Livewire\Estudiante\MisCursos;
use App\Models\Administrador;
use App\Models\Configuracion;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NequiPagoTest extends TestCase
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

    private function crearEstudiante(): User
    {
        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Test',
            'role' => 'estu',
        ]);

        Estudiante::create([
            'codigo' => 'EST-001',
            'user_id' => $user->id,
        ]);

        return $user;
    }

    public function test_admin_puede_guardar_los_datos_de_nequi(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('nequi_numero', '300 123 4567')
            ->set('nequi_titular', 'Fundación Yama')
            ->set('nequi_instrucciones', 'Escanea el QR y envía el comprobante.')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertSame('300 123 4567', Configuracion::obtener('nequi_numero'));
        $this->assertSame('Fundación Yama', Configuracion::obtener('nequi_titular'));
        $this->assertSame('Escanea el QR y envía el comprobante.', Configuracion::obtener('nequi_instrucciones'));
    }

    public function test_admin_puede_subir_el_codigo_qr_de_nequi(): void
    {
        Storage::fake('public');

        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('nequi_qr', UploadedFile::fake()->image('nequi-qr.png'))
            ->call('guardar')
            ->assertHasNoErrors();

        $ruta = Configuracion::obtener('nequi_qr');

        $this->assertNotNull($ruta);
        Storage::disk('public')->assertExists($ruta);
    }

    public function test_el_qr_de_nequi_no_debe_superar_el_tamano_maximo(): void
    {
        Storage::fake('public');

        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('nequi_qr', UploadedFile::fake()->image('nequi-qr.png')->size(4096))
            ->call('guardar')
            ->assertHasErrors(['nequi_qr']);
    }

    public function test_la_pagina_principal_muestra_el_qr_y_los_datos_de_nequi(): void
    {
        Configuracion::establecer('nequi_numero', '300 123 4567', 'texto', 'pagos');
        Configuracion::establecer('nequi_titular', 'Fundación Yama', 'texto', 'pagos');
        Configuracion::establecer('nequi_instrucciones', 'Escanea el QR para pagar.', 'texto', 'pagos');

        $this->get('/')
            ->assertOk()
            ->assertSee('Paga fácil con Nequi')
            ->assertSee('300 123 4567')
            ->assertSee('Fundación Yama')
            ->assertSee('Escanea el QR para pagar.');
    }

    public function test_la_pagina_principal_no_muestra_la_seccion_si_no_hay_datos_de_nequi(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertDontSee('Paga fácil con Nequi');
    }

    public function test_estudiante_puede_abrir_el_modal_de_pago_con_nequi(): void
    {
        $estudiante = $this->crearEstudiante();

        Livewire::actingAs($estudiante)
            ->test(MisCursos::class)
            ->call('abrirModalNequi', 'Curso de Prueba')
            ->assertSet('showModalNequi', true)
            ->assertSet('cursoPagoNombre', 'Curso de Prueba')
            ->assertSee('Pago con Nequi');
    }

    public function test_estudiante_puede_cerrar_el_modal_de_pago_con_nequi(): void
    {
        $estudiante = $this->crearEstudiante();

        Livewire::actingAs($estudiante)
            ->test(MisCursos::class)
            ->call('abrirModalNequi', 'Curso de Prueba')
            ->call('cerrarModalNequi')
            ->assertSet('showModalNequi', false)
            ->assertSet('cursoPagoNombre', '');
    }

    public function test_el_modal_de_nequi_muestra_los_datos_configurados(): void
    {
        Configuracion::establecer('nequi_numero', '311 555 7788', 'texto', 'pagos');
        Configuracion::establecer('nequi_titular', 'Fundación Yama', 'texto', 'pagos');

        $estudiante = $this->crearEstudiante();

        Livewire::actingAs($estudiante)
            ->test(MisCursos::class)
            ->call('abrirModalNequi', 'Curso de Prueba')
            ->assertSee('311 555 7788')
            ->assertSee('Fundación Yama');
    }
}
