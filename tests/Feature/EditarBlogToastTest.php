<?php

namespace Tests\Feature;

use App\Livewire\Admin\Blog\EditarBlog;
use App\Livewire\Admin\Blog\IndexBlog;
use App\Models\Administrador;
use App\Models\Articulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditarBlogToastTest extends TestCase
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

    private function crearArticulo(Administrador $admin): Articulo
    {
        return Articulo::create([
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
    }

    public function test_actualizar_articulo_guarda_mensaje_de_exito_en_sesion(): void
    {
        $admin = $this->crearAdmin();
        $articulo = $this->crearArticulo($admin);

        Livewire::test(EditarBlog::class, ['articulo' => $articulo])
            ->set('titulo', 'Título actualizado')
            ->call('actualizar')
            ->assertRedirect(route('admin.blog.index'));

        $this->assertSame('Artículo actualizado exitosamente.', session('success'));
    }

    public function test_index_blog_despacha_toast_cuando_hay_mensaje_en_sesion(): void
    {
        $this->crearAdmin();

        session()->flash('success', 'Artículo actualizado exitosamente.');

        Livewire::test(IndexBlog::class)
            ->assertDispatched('show-toast', type: 'success', message: 'Artículo actualizado exitosamente.');
    }
}
