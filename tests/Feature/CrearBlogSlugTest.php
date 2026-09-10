<?php

namespace Tests\Feature;

use App\Livewire\Admin\Blog\CrearBlog;
use App\Livewire\Admin\Blog\EditarBlog;
use App\Models\Administrador;
use App\Models\Articulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CrearBlogSlugTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): Administrador
    {
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user);

        return Administrador::create([
            'user_id' => $user->id,
            'departamento' => 'Comunicaciones',
            'cargo' => 'Editor',
            'super_admin' => true,
        ]);
    }

    public function test_el_slug_se_genera_automaticamente_desde_el_titulo(): void
    {
        $this->crearAdmin();

        Livewire::test(CrearBlog::class)
            ->set('titulo', 'Cómo Aprender Laravel Rápidamente en 2024')
            ->assertSet('slug', 'como-aprender-laravel-rapidamente-en-2024');
    }

    public function test_el_slug_se_actualiza_al_cambiar_el_titulo(): void
    {
        $this->crearAdmin();

        Livewire::test(CrearBlog::class)
            ->set('titulo', 'Primer título')
            ->assertSet('slug', 'primer-titulo')
            ->set('titulo', 'Segundo título distinto')
            ->assertSet('slug', 'segundo-titulo-distinto');
    }

    public function test_el_slug_se_regenera_al_editar_el_titulo(): void
    {
        $admin = $this->crearAdmin();

        $articulo = Articulo::create([
            'titulo' => 'Título original',
            'slug' => 'titulo-original',
            'resumen' => 'Resumen de prueba suficientemente largo.',
            'contenido' => str_repeat('Contenido de prueba. ', 5),
            'categoria' => 'general',
            'autor' => 'Autor',
            'publicado' => true,
            'fecha_publicacion' => now(),
            'autor_id_admin' => $admin->idAdmin,
        ]);

        Livewire::test(EditarBlog::class, ['articulo' => $articulo])
            ->assertSet('slug', 'titulo-original')
            ->set('titulo', 'Título Modificado')
            ->assertSet('slug', 'titulo-modificado');
    }
}
