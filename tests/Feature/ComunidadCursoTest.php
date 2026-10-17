<?php

namespace Tests\Feature;

use App\Livewire\Admin\Cursos\EditarCurso;
use App\Livewire\Admin\Cursos\GestionarRecursosCurso;
use App\Livewire\Admin\Cursos\MostrarCurso;
use App\Livewire\Estudiante\ComunidadCurso;
use App\Models\Curso;
use App\Models\CursoNetworking;
use App\Models\CursoPregunta;
use App\Models\CursoRecurso;
use App\Models\CursoRespuesta;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ComunidadCursoTest extends TestCase
{
    use RefreshDatabase;

    private function crearCurso(string $codigo = 'CUR-2026-001'): Curso
    {
        return Curso::create([
            'codigo' => $codigo,
            'nombre' => 'Curso '.$codigo,
            'slug' => 'curso-'.strtolower($codigo),
            'descripcion' => 'Curso de prueba.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'objetivos' => 'Objetivos de prueba',
            'materiales_incluidos' => 'Materiales de prueba',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'duracion_horas' => 5,
            'duracion_texto' => '5 horas',
            'precio_regular' => 100,
            'precio_descuento' => null,
            'nivel' => 'principiante',
            'publicado' => true,
            'destacado' => false,
            'comunidad_habilitada' => true,
            'comunidad_tras_finalizar' => false,
            'fecha_inicio' => now()->toDateString(),
            'fecha_fin' => now()->addDays(7)->toDateString(),
        ]);
    }

    private function crearEstudiante(string $codigo = 'EST-TEST-001'): Estudiante
    {
        $user = User::factory()->create([
            'name' => 'Estudiante',
            'apellido' => 'Test',
            'role' => 'estu',
        ]);

        return Estudiante::create([
            'codigo' => $codigo,
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);
    }

    private function crearAdmin(): User
    {
        return User::factory()->create([
            'name' => 'Admin',
            'apellido' => 'Test',
            'role' => 'admin',
        ]);
    }

    private function inscribir(Estudiante $estudiante, string $cursoCodigo): void
    {
        $estudiante->cursos()->attach($cursoCodigo, [
            'estado' => 'en_progreso',
            'estado_pago' => 'completo',
            'pago_realizado' => 100,
            'fecha_inscripcion' => now(),
            'progreso' => 10,
        ]);
    }

    private function inscribirCompletado(Estudiante $estudiante, string $cursoCodigo): void
    {
        $estudiante->cursos()->attach($cursoCodigo, [
            'estado' => 'completado',
            'estado_pago' => 'completo',
            'pago_realizado' => 100,
            'fecha_inscripcion' => now(),
            'progreso' => 100,
        ]);
    }

    public function test_estudiante_inscrito_puede_acceder_a_la_comunidad(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertOk()
            ->assertSet('tab', 'preguntas');
    }

    public function test_estudiante_no_inscrito_no_puede_acceder_a_la_comunidad(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertStatus(403);
    }

    public function test_estudiante_inscrito_puede_crear_pregunta(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('abrirModalPregunta')
            ->set('tituloPregunta', '¿Cómo instalo el entorno?')
            ->set('contenidoPregunta', 'Necesito ayuda con la instalación inicial del entorno de trabajo.')
            ->call('crearPregunta')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('curso_preguntas', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'titulo' => '¿Cómo instalo el entorno?',
        ]);
    }

    public function test_crear_pregunta_valida_campos_requeridos(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('abrirModalPregunta')
            ->set('tituloPregunta', '')
            ->set('contenidoPregunta', '')
            ->call('crearPregunta')
            ->assertHasErrors(['tituloPregunta' => 'required', 'contenidoPregunta' => 'required']);

        $this->assertDatabaseCount('curso_preguntas', 0);
    }

    public function test_estudiante_puede_responder_una_pregunta(): void
    {
        $curso = $this->crearCurso();
        $autor = $this->crearEstudiante('EST-AUTOR-001');
        $companero = $this->crearEstudiante('EST-COMPA-002');
        $this->inscribir($autor, $curso->codigo);
        $this->inscribir($companero, $curso->codigo);

        $pregunta = CursoPregunta::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $autor->codigo,
            'titulo' => 'Pregunta de prueba',
            'contenido' => 'Contenido de la pregunta de prueba.',
        ]);

        Livewire::actingAs($companero->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('seleccionarPregunta', $pregunta->id)
            ->set('nuevaRespuesta', 'Yo tuve el mismo problema y lo resolví así.')
            ->call('responderPregunta')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('curso_respuestas', [
            'pregunta_id' => $pregunta->id,
            'estudiante_id' => $companero->codigo,
        ]);
    }

    public function test_solo_el_autor_puede_marcar_pregunta_como_resuelta(): void
    {
        $curso = $this->crearCurso();
        $autor = $this->crearEstudiante('EST-AUTOR-001');
        $otro = $this->crearEstudiante('EST-OTRO-002');
        $this->inscribir($autor, $curso->codigo);
        $this->inscribir($otro, $curso->codigo);

        $pregunta = CursoPregunta::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $autor->codigo,
            'titulo' => 'Pregunta de prueba',
            'contenido' => 'Contenido de la pregunta de prueba.',
        ]);

        Livewire::actingAs($otro->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('marcarResuelta', $pregunta->id)
            ->assertForbidden();

        $this->assertFalse($pregunta->fresh()->resuelta);

        Livewire::actingAs($autor->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('marcarResuelta', $pregunta->id)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertTrue($pregunta->fresh()->resuelta);
    }

    public function test_estudiante_puede_publicar_mensaje_de_networking(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->set('tab', 'networking')
            ->set('nuevoMensaje', 'Hola, soy diseñador y busco colaborar en un proyecto.')
            ->set('tipoMensaje', 'colaboracion')
            ->call('publicarMensaje')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('curso_networking', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'tipo' => 'colaboracion',
        ]);
    }

    public function test_publicar_mensaje_valida_tipo_permitido(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->set('nuevoMensaje', 'Mensaje válido para la prueba.')
            ->set('tipoMensaje', 'invalido')
            ->call('publicarMensaje')
            ->assertHasErrors(['tipoMensaje']);

        $this->assertDatabaseCount('curso_networking', 0);
    }

    public function test_estudiante_solo_ve_recursos_publicados(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        CursoRecurso::create([
            'curso_id' => $curso->codigo,
            'titulo' => 'Plantilla visible',
            'tipo' => 'plantilla',
            'publicado' => true,
        ]);

        CursoRecurso::create([
            'curso_id' => $curso->codigo,
            'titulo' => 'Plantilla oculta',
            'tipo' => 'plantilla',
            'publicado' => false,
        ]);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->set('tab', 'recursos')
            ->assertSee('Plantilla visible')
            ->assertDontSee('Plantilla oculta');
    }

    public function test_admin_puede_crear_recurso_descargable(): void
    {
        $curso = $this->crearCurso();
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(GestionarRecursosCurso::class, ['curso' => $curso])
            ->call('abrirModal')
            ->set('titulo', 'Guía de inicio rápido')
            ->set('descripcion', 'Guía lista para aplicar en el mundo real.')
            ->set('tipo', 'guia')
            ->set('version', '2.0')
            ->call('guardar')
            ->assertDispatched('show-toast', type: 'success');

        $this->assertDatabaseHas('curso_recursos', [
            'curso_id' => $curso->codigo,
            'titulo' => 'Guía de inicio rápido',
            'tipo' => 'guia',
            'version' => '2.0',
        ]);
    }

    public function test_estudiante_no_puede_gestionar_recursos(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(GestionarRecursosCurso::class, ['curso' => $curso])
            ->assertStatus(403);
    }

    public function test_admin_puede_ocultar_y_eliminar_recurso(): void
    {
        $curso = $this->crearCurso();
        $admin = $this->crearAdmin();

        $recurso = CursoRecurso::create([
            'curso_id' => $curso->codigo,
            'titulo' => 'Recurso de prueba',
            'tipo' => 'recurso',
            'publicado' => true,
        ]);

        Livewire::actingAs($admin)
            ->test(GestionarRecursosCurso::class, ['curso' => $curso])
            ->call('togglePublicado', $recurso->id)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertFalse($recurso->fresh()->publicado);

        Livewire::actingAs($admin)
            ->test(GestionarRecursosCurso::class, ['curso' => $curso])
            ->call('eliminarRecurso', $recurso->id)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertSoftDeleted('curso_recursos', ['id' => $recurso->id]);
    }

    public function test_estudiante_puede_eliminar_su_propia_respuesta(): void
    {
        $curso = $this->crearCurso();
        $autor = $this->crearEstudiante('EST-AUTOR-001');
        $this->inscribir($autor, $curso->codigo);

        $pregunta = CursoPregunta::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $autor->codigo,
            'titulo' => 'Pregunta de prueba',
            'contenido' => 'Contenido de la pregunta de prueba.',
        ]);

        $respuesta = CursoRespuesta::create([
            'pregunta_id' => $pregunta->id,
            'estudiante_id' => $autor->codigo,
            'contenido' => 'Respuesta propia del autor.',
        ]);

        Livewire::actingAs($autor->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('eliminarRespuesta', $respuesta->id)
            ->assertDispatched('show-toast', type: 'success');

        $this->assertSoftDeleted('curso_respuestas', ['id' => $respuesta->id]);
    }

    public function test_estudiante_no_puede_eliminar_publicacion_de_otro(): void
    {
        $curso = $this->crearCurso();
        $autor = $this->crearEstudiante('EST-AUTOR-001');
        $otro = $this->crearEstudiante('EST-OTRO-002');
        $this->inscribir($autor, $curso->codigo);
        $this->inscribir($otro, $curso->codigo);

        $publicacion = CursoNetworking::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $autor->codigo,
            'mensaje' => 'Publicación del autor original.',
            'tipo' => 'mensaje',
        ]);

        Livewire::actingAs($otro->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('eliminarPublicacion', $publicacion->id)
            ->assertForbidden();

        $this->assertDatabaseHas('curso_networking', ['id' => $publicacion->id]);
    }

    public function test_curso_completado_marca_la_comunidad_como_deshabilitada_por_defecto(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertOk()
            ->assertSet('cursoCompletado', true)
            ->assertSet('comunidadDeshabilitada', true)
            ->assertSee('Comunidad deshabilitada');
    }

    public function test_curso_en_progreso_no_marca_la_comunidad_como_deshabilitada(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertOk()
            ->assertSet('cursoCompletado', false)
            ->assertSet('comunidadDeshabilitada', false)
            ->assertDontSee('Comunidad deshabilitada');
    }

    public function test_admin_puede_mantener_la_comunidad_habilitada_tras_finalizar(): void
    {
        $curso = $this->crearCurso();
        $curso->update(['comunidad_tras_finalizar' => true]);

        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertOk()
            ->assertSet('cursoCompletado', true)
            ->assertSet('comunidadDeshabilitada', false)
            ->assertDontSee('Comunidad deshabilitada');
    }

    public function test_admin_puede_deshabilitar_la_comunidad_para_curso_en_progreso(): void
    {
        $curso = $this->crearCurso();
        $curso->update(['comunidad_habilitada' => false]);

        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertOk()
            ->assertSet('comunidadDeshabilitada', true)
            ->assertSee('Comunidad deshabilitada');
    }

    public function test_estudiante_con_curso_completado_puede_crear_pregunta_si_admin_lo_permite(): void
    {
        $curso = $this->crearCurso();
        $curso->update(['comunidad_tras_finalizar' => true]);

        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->set('tituloPregunta', 'Pregunta tras finalizar')
            ->set('contenidoPregunta', 'Contenido de la pregunta tras finalizar el curso.')
            ->call('crearPregunta')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_preguntas', [
            'curso_id' => $curso->codigo,
            'titulo' => 'Pregunta tras finalizar',
        ]);
    }

    public function test_estudiante_con_curso_completado_no_puede_crear_pregunta(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->set('tituloPregunta', 'Pregunta tras finalizar')
            ->set('contenidoPregunta', 'Contenido de la pregunta tras finalizar el curso.')
            ->call('crearPregunta')
            ->assertStatus(403);

        $this->assertDatabaseMissing('curso_preguntas', [
            'curso_id' => $curso->codigo,
            'titulo' => 'Pregunta tras finalizar',
        ]);
    }

    public function test_estudiante_con_curso_completado_no_puede_publicar_mensaje(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->set('nuevoMensaje', 'Mensaje tras finalizar el curso.')
            ->call('publicarMensaje')
            ->assertStatus(403);

        $this->assertDatabaseMissing('curso_networking', [
            'curso_id' => $curso->codigo,
            'mensaje' => 'Mensaje tras finalizar el curso.',
        ]);
    }

    public function test_estudiante_con_curso_completado_no_puede_eliminar_su_pregunta(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        $pregunta = CursoPregunta::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'titulo' => 'Pregunta previa',
            'contenido' => 'Contenido de la pregunta previa al cierre.',
        ]);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('eliminarPregunta', $pregunta->id)
            ->assertStatus(403);

        $this->assertDatabaseHas('curso_preguntas', [
            'id' => $pregunta->id,
            'deleted_at' => null,
        ]);
    }

    public function test_estudiante_con_curso_completado_puede_seguir_viendo_el_contenido(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        CursoPregunta::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'titulo' => 'Pregunta histórica',
            'contenido' => 'Contenido visible en modo solo lectura.',
        ]);

        CursoRecurso::create([
            'curso_id' => $curso->codigo,
            'titulo' => 'Plantilla histórica',
            'tipo' => 'plantilla',
            'publicado' => true,
        ]);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->assertOk()
            ->assertSee('Pregunta histórica')
            ->set('tab', 'recursos')
            ->assertSee('Plantilla histórica')
            ->assertSee('Descarga no disponible');
    }

    public function test_estudiante_con_curso_completado_no_puede_descargar_recursos(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        $recurso = CursoRecurso::create([
            'curso_id' => $curso->codigo,
            'titulo' => 'Plantilla bloqueada',
            'tipo' => 'plantilla',
            'archivo_path' => 'recursos-cursos/plantilla.pdf',
            'publicado' => true,
        ]);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('descargarRecurso', $recurso->id)
            ->assertStatus(403);
    }

    public function test_estudiante_con_curso_completado_puede_descargar_si_admin_lo_permite(): void
    {
        $curso = $this->crearCurso();
        $curso->update(['comunidad_tras_finalizar' => true]);

        $estudiante = $this->crearEstudiante();
        $this->inscribirCompletado($estudiante, $curso->codigo);

        $recurso = CursoRecurso::create([
            'curso_id' => $curso->codigo,
            'titulo' => 'Plantilla permitida',
            'tipo' => 'plantilla',
            'archivo_path' => 'recursos-cursos/plantilla.pdf',
            'publicado' => true,
        ]);

        Livewire::actingAs($estudiante->user)
            ->test(ComunidadCurso::class, ['curso' => $curso])
            ->call('descargarRecurso', $recurso->id)
            ->assertOk();
    }

    public function test_admin_puede_alternar_la_comunidad_desde_el_panel(): void
    {
        $curso = $this->crearCurso();
        $admin = $this->crearAdmin();

        $this->assertTrue($curso->comunidad_habilitada);

        Livewire::actingAs($admin)
            ->test(MostrarCurso::class, ['curso' => $curso])
            ->call('toggleComunidad')
            ->assertHasNoErrors();

        $this->assertFalse($curso->fresh()->comunidad_habilitada);

        Livewire::actingAs($admin)
            ->test(MostrarCurso::class, ['curso' => $curso])
            ->call('toggleComunidad')
            ->assertHasNoErrors();

        $this->assertTrue($curso->fresh()->comunidad_habilitada);
    }

    public function test_admin_puede_alternar_la_comunidad_tras_finalizar_desde_el_panel(): void
    {
        $curso = $this->crearCurso();
        $admin = $this->crearAdmin();

        $this->assertFalse($curso->comunidad_tras_finalizar);

        Livewire::actingAs($admin)
            ->test(MostrarCurso::class, ['curso' => $curso])
            ->call('toggleComunidadTrasFinalizar')
            ->assertHasNoErrors();

        $this->assertTrue($curso->fresh()->comunidad_tras_finalizar);
    }

    public function test_estudiante_no_puede_alternar_la_comunidad(): void
    {
        $curso = $this->crearCurso();
        $estudiante = $this->crearEstudiante();
        $this->inscribir($estudiante, $curso->codigo);

        Livewire::actingAs($estudiante->user)
            ->test(MostrarCurso::class, ['curso' => $curso])
            ->call('toggleComunidad')
            ->assertForbidden();

        $this->assertTrue($curso->fresh()->comunidad_habilitada);
    }

    public function test_admin_puede_configurar_la_comunidad_al_editar_el_curso(): void
    {
        $curso = $this->crearCurso();
        $admin = $this->crearAdmin();

        Livewire::actingAs($admin)
            ->test(EditarCurso::class, ['curso' => $curso])
            ->set('descripcion', str_repeat('Descripción válida del curso. ', 3))
            ->set('objetivos', str_repeat('Objetivo válido del curso. ', 2))
            ->set('comunidad_habilitada', true)
            ->set('comunidad_tras_finalizar', true)
            ->call('actualizarCurso')
            ->assertHasNoErrors();

        $curso->refresh();
        $this->assertTrue($curso->comunidad_habilitada);
        $this->assertTrue($curso->comunidad_tras_finalizar);
    }
}
