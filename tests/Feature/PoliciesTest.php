<?php

namespace Tests\Feature;

use App\Models\Administrador;
use App\Models\Articulo;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Evento;
use App\Models\Solicitud;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PoliciesTest extends TestCase
{
    use RefreshDatabase;

    private function crearAdmin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function crearAdministrador(User $admin): Administrador
    {
        return Administrador::create([
            'user_id' => $admin->id,
            'departamento' => 'Dirección',
            'cargo' => 'Administrador',
            'super_admin' => true,
            'fecha_ingreso' => now(),
        ]);
    }

    private function crearEstudianteUser(): User
    {
        return User::factory()->create(['role' => 'estu']);
    }

    private function crearEstudiante(User $user): Estudiante
    {
        return Estudiante::create([
            'codigo' => 'EST-'.fake()->unique()->numerify('####'),
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);
    }

    private function crearEvento(): Evento
    {
        $admin = $this->crearAdmin();
        $administrador = $this->crearAdministrador($admin);

        return Evento::create([
            'titulo' => 'Evento de prueba',
            'slug' => 'evento-de-prueba',
            'descripcion' => 'Descripción del evento de prueba.',
            'fecha' => now()->addDays(10),
            'hora_inicio' => '10:00:00',
            'hora_fin' => '12:00:00',
            'ubicacion' => 'Auditorio principal',
            'tipo_evento' => 'presencial',
            'costo' => 0,
            'publicado' => true,
            'creado_por_admin' => $administrador->idAdmin,
        ]);
    }

    private function crearArticulo(): Articulo
    {
        $admin = $this->crearAdmin();
        $administrador = $this->crearAdministrador($admin);

        return Articulo::create([
            'titulo' => 'Artículo de prueba',
            'slug' => 'articulo-de-prueba',
            'resumen' => 'Resumen del artículo de prueba para validar la política.',
            'contenido' => 'Contenido del artículo de prueba para validar la política de autorización.',
            'categoria' => 'general',
            'autor' => 'Administración',
            'publicado' => true,
            'autor_id_admin' => $administrador->idAdmin,
        ]);
    }

    private function crearCertificado(Estudiante $estudiante, Curso $curso): Certificado
    {
        return Certificado::create([
            'estudiante_id' => $estudiante->codigo,
            'curso_id' => $curso->codigo,
            'numero_certificado' => 'CERT-'.fake()->unique()->numerify('####'),
            'fecha_emision' => now(),
        ]);
    }

    private function crearSolicitud(User $user): Solicitud
    {
        return Solicitud::create([
            'tipo' => 'inscripcion',
            'asunto' => 'Solicitud de inscripción',
            'mensaje' => 'Quiero inscribirme en un curso.',
            'email_contacto' => $user->email,
            'estado' => 'pendiente',
            'user_id' => $user->id,
        ]);
    }

    // ─── EstudiantePolicy ───────────────────────────────────────────────

    public function test_admin_puede_actualizar_cualquier_estudiante(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante($this->crearEstudianteUser());

        $this->actingAs($admin);

        $this->assertTrue($admin->can('update', $estudiante));
    }

    public function test_estudiante_puede_actualizar_su_propio_perfil(): void
    {
        $user = $this->crearEstudianteUser();
        $estudiante = $this->crearEstudiante($user);

        $this->actingAs($user);

        $this->assertTrue($user->can('update', $estudiante));
    }

    public function test_estudiante_no_puede_actualizar_perfil_de_otro(): void
    {
        $user = $this->crearEstudianteUser();
        $otroEstudiante = $this->crearEstudiante($this->crearEstudianteUser());

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $otroEstudiante));
    }

    public function test_estudiante_no_puede_eliminar_estudiantes(): void
    {
        $user = $this->crearEstudianteUser();
        $estudiante = $this->crearEstudiante($user);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $estudiante));
    }

    public function test_admin_puede_eliminar_estudiantes(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante($this->crearEstudianteUser());

        $this->actingAs($admin);

        $this->assertTrue($admin->can('delete', $estudiante));
    }

    // ─── EventoPolicy ───────────────────────────────────────────────────

    public function test_admin_puede_actualizar_eventos(): void
    {
        $admin = $this->crearAdmin();
        $evento = $this->crearEvento();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('update', $evento));
    }

    public function test_estudiante_no_puede_actualizar_eventos(): void
    {
        $user = $this->crearEstudianteUser();
        $evento = $this->crearEvento();

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $evento));
    }

    public function test_estudiante_no_puede_eliminar_eventos(): void
    {
        $user = $this->crearEstudianteUser();
        $evento = $this->crearEvento();

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $evento));
    }

    // ─── ArticuloPolicy ─────────────────────────────────────────────────

    public function test_admin_puede_actualizar_articulos(): void
    {
        $admin = $this->crearAdmin();
        $articulo = $this->crearArticulo();

        $this->actingAs($admin);

        $this->assertTrue($admin->can('update', $articulo));
    }

    public function test_estudiante_no_puede_actualizar_articulos(): void
    {
        $user = $this->crearEstudianteUser();
        $articulo = $this->crearArticulo();

        $this->actingAs($user);

        $this->assertFalse($user->can('update', $articulo));
    }

    public function test_estudiante_no_puede_eliminar_articulos(): void
    {
        $user = $this->crearEstudianteUser();
        $articulo = $this->crearArticulo();

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $articulo));
    }

    // ─── CertificadoPolicy ──────────────────────────────────────────────

    public function test_estudiante_puede_ver_sus_propios_certificados(): void
    {
        $user = $this->crearEstudianteUser();
        $estudiante = $this->crearEstudiante($user);
        $curso = Curso::create([
            'codigo' => 'CUR-'.fake()->unique()->numerify('####'),
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'descripcion' => 'Descripción del curso de prueba.',
            'cronograma' => 'Cronograma del curso.',
            'requisitos' => 'Requisitos del curso.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);
        $certificado = $this->crearCertificado($estudiante, $curso);

        $this->actingAs($user);

        $this->assertTrue($user->can('view', $certificado));
    }

    public function test_estudiante_no_puede_ver_certificados_de_otros(): void
    {
        $user = $this->crearEstudianteUser();
        $otroEstudiante = $this->crearEstudiante($this->crearEstudianteUser());
        $curso = Curso::create([
            'codigo' => 'CUR-'.fake()->unique()->numerify('####'),
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'descripcion' => 'Descripción del curso de prueba.',
            'cronograma' => 'Cronograma del curso.',
            'requisitos' => 'Requisitos del curso.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);
        $certificado = $this->crearCertificado($otroEstudiante, $curso);

        $this->actingAs($user);

        $this->assertFalse($user->can('view', $certificado));
    }

    public function test_estudiante_no_puede_eliminar_certificados(): void
    {
        $user = $this->crearEstudianteUser();
        $estudiante = $this->crearEstudiante($user);
        $curso = Curso::create([
            'codigo' => 'CUR-'.fake()->unique()->numerify('####'),
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'descripcion' => 'Descripción del curso de prueba.',
            'cronograma' => 'Cronograma del curso.',
            'requisitos' => 'Requisitos del curso.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);
        $certificado = $this->crearCertificado($estudiante, $curso);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $certificado));
    }

    // ─── SolicitudPolicy ────────────────────────────────────────────────

    public function test_usuario_puede_ver_sus_propias_solicitudes(): void
    {
        $user = $this->crearEstudianteUser();
        $solicitud = $this->crearSolicitud($user);

        $this->actingAs($user);

        $this->assertTrue($user->can('view', $solicitud));
    }

    public function test_usuario_no_puede_ver_solicitudes_de_otros(): void
    {
        $user = $this->crearEstudianteUser();
        $otraSolicitud = $this->crearSolicitud($this->crearEstudianteUser());

        $this->actingAs($user);

        $this->assertFalse($user->can('view', $otraSolicitud));
    }

    public function test_admin_puede_ver_cualquier_solicitud(): void
    {
        $admin = $this->crearAdmin();
        $solicitud = $this->crearSolicitud($this->crearEstudianteUser());

        $this->actingAs($admin);

        $this->assertTrue($admin->can('view', $solicitud));
    }

    public function test_usuario_puede_crear_solicitudes(): void
    {
        $user = $this->crearEstudianteUser();

        $this->actingAs($user);

        $this->assertTrue($user->can('create', Solicitud::class));
    }

    public function test_usuario_no_puede_eliminar_solicitudes(): void
    {
        $user = $this->crearEstudianteUser();
        $solicitud = $this->crearSolicitud($user);

        $this->actingAs($user);

        $this->assertFalse($user->can('delete', $solicitud));
    }
}
