<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Articulo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticuloContenidoHtmlTest extends TestCase
{
    use RefreshDatabase;

    private function crearArticulo(string $contenido): Articulo
    {
        $user = User::factory()->create(['role' => 'admin']);

        $admin = Administrador::create([
            'user_id' => $user->id,
            'departamento' => 'Comunicaciones',
            'cargo' => 'Editor',
            'super_admin' => true,
        ]);

        return Articulo::create([
            'titulo' => 'Artículo de prueba',
            'slug' => 'articulo-de-prueba',
            'resumen' => 'Resumen de prueba',
            'contenido' => $contenido,
            'categoria' => 'general',
            'autor' => 'Autor',
            'publicado' => true,
            'fecha_publicacion' => now(),
            'autor_id_admin' => $admin->idAdmin,
        ]);
    }

    public function test_texto_plano_se_convierte_en_parrafos(): void
    {
        $articulo = $this->crearArticulo("Primer párrafo.\n\nSegundo párrafo.");

        $html = $articulo->contenido_html;

        $this->assertStringContainsString('<p>Primer párrafo.</p>', $html);
        $this->assertStringContainsString('<p>Segundo párrafo.</p>', $html);
    }

    public function test_saltos_de_linea_simples_se_conservan_como_br(): void
    {
        $articulo = $this->crearArticulo("Línea uno\nLínea dos");

        $html = $articulo->contenido_html;

        $this->assertStringContainsString('Línea uno<br', $html);
        $this->assertStringContainsString('Línea dos', $html);
    }

    public function test_html_permitido_se_conserva(): void
    {
        $articulo = $this->crearArticulo('<p>Texto <strong>importante</strong></p>');

        $html = $articulo->contenido_html;

        $this->assertStringContainsString('<strong>importante</strong>', $html);
    }

    public function test_scripts_peligrosos_se_eliminan(): void
    {
        $articulo = $this->crearArticulo('<p>Seguro</p><script>alert("xss")</script>');

        $html = $articulo->contenido_html;

        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('Seguro', $html);
    }

    public function test_contenido_vacio_devuelve_cadena_vacia(): void
    {
        $articulo = $this->crearArticulo('');

        $this->assertSame('', $articulo->contenido_html);
    }
}
