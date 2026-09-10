<?php

namespace Tests\Feature;

use App\Livewire\Admin\PersonalizarSitio;
use App\Models\Administrador;
use App\Models\Configuracion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersonalizarSitioTest extends TestCase
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

    public function test_admin_puede_acceder_a_la_pagina_de_personalizacion(): void
    {
        $admin = $this->crearAdmin();

        $this->actingAs($admin)
            ->get(route('admin.personalizar'))
            ->assertOk()
            ->assertSee('Personalizar Sitio');
    }

    public function test_estudiante_no_puede_acceder_a_la_personalizacion(): void
    {
        $estudiante = User::factory()->create(['role' => 'estu']);

        $this->actingAs($estudiante)
            ->get(route('admin.personalizar'))
            ->assertForbidden();
    }

    public function test_guarda_las_estadisticas_y_la_tarjeta(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('stat_estudiantes', '500+')
            ->set('stat_cursos', '30+')
            ->set('stat_experiencia', '18+')
            ->set('stat_satisfaccion', '99%')
            ->set('about_anios', '18+')
            ->set('about_titulo', 'Años de impacto')
            ->set('about_descripcion', 'Descripción personalizada de la fundación.')
            ->set('beneficios', ['Beneficio uno', 'Beneficio dos', 'Beneficio tres'])
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertSame('500+', Configuracion::obtener('stat_estudiantes'));
        $this->assertSame('30+', Configuracion::obtener('stat_cursos'));
        $this->assertSame('18+', Configuracion::obtener('stat_experiencia'));
        $this->assertSame('99%', Configuracion::obtener('stat_satisfaccion'));
        $this->assertSame('18+', Configuracion::obtener('about_anios'));
        $this->assertSame('Años de impacto', Configuracion::obtener('about_titulo'));
        $this->assertSame('Beneficio uno', Configuracion::obtener('beneficio_1'));
        $this->assertSame('Beneficio tres', Configuracion::obtener('beneficio_3'));
    }

    public function test_valida_campos_obligatorios(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('stat_estudiantes', '')
            ->set('about_titulo', '')
            ->call('guardar')
            ->assertHasErrors(['stat_estudiantes', 'about_titulo']);
    }

    public function test_la_pagina_principal_muestra_los_valores_configurados(): void
    {
        Configuracion::establecer('stat_estudiantes', '777+', 'texto', 'estadisticas');
        Configuracion::establecer('about_titulo', 'Título personalizado', 'texto', 'nosotros');

        $this->get('/')
            ->assertOk()
            ->assertSee('777+')
            ->assertSee('Título personalizado');
    }

    public function test_guarda_los_datos_de_contacto(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('contacto_email', 'contacto@funyama.org')
            ->set('contacto_whatsapp_1', '300 111 2233')
            ->set('contacto_whatsapp_2', '300 444 5566')
            ->call('guardar')
            ->assertHasNoErrors()
            ->assertRedirect(route('home'));

        $this->assertSame('contacto@funyama.org', Configuracion::obtener('contacto_email'));
        $this->assertSame('300 111 2233', Configuracion::obtener('contacto_whatsapp_1'));
        $this->assertSame('300 444 5566', Configuracion::obtener('contacto_whatsapp_2'));
    }

    public function test_valida_los_datos_de_contacto(): void
    {
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(PersonalizarSitio::class)
            ->set('contacto_email', 'no-es-un-correo')
            ->set('contacto_whatsapp_1', '')
            ->call('guardar')
            ->assertHasErrors(['contacto_email', 'contacto_whatsapp_1']);
    }

    public function test_la_pagina_principal_muestra_los_datos_de_contacto_configurados(): void
    {
        Configuracion::establecer('contacto_email', 'nuevo@funyama.org', 'texto', 'contacto');
        Configuracion::establecer('contacto_whatsapp_1', '300 999 8877', 'texto', 'contacto');

        $this->get('/')
            ->assertOk()
            ->assertSee('nuevo@funyama.org')
            ->assertSee('300 999 8877')
            ->assertSee('573009998877');
    }
}
