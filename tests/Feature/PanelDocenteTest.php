<?php

namespace Tests\Feature;

use App\Models\Asistencia;
use App\Models\Curso;
use App\Models\Docente;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Maatwebsite\Excel\Facades\Excel;
use Tests\TestCase;

class PanelDocenteTest extends TestCase
{
    use RefreshDatabase;

    private function crearDocente(): User
    {
        $user = User::factory()->create(['role' => 'instructor']);
        Docente::create([
            'user_id' => $user->id,
            'especialidad' => 'Matemáticas',
            'activo' => true,
        ]);

        return $user->fresh();
    }

    private function crearCurso(User $docente, array $overrides = []): Curso
    {
        return Curso::create(array_merge([
            'codigo' => 'CUR-'.uniqid(),
            'nombre' => 'Curso de Prueba',
            'slug' => 'curso-de-prueba-'.uniqid(),
            'descripcion' => str_repeat('Descripción del curso. ', 5),
            'cronograma' => 'Lunes y miércoles de 18:00 a 20:00',
            'requisitos' => 'Conocimientos básicos previos recomendados',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'instructor_id' => $docente->id,
            'publicado' => true,
            'comunidad_habilitada' => true,
            'comunidad_tras_finalizar' => false,
        ], $overrides));
    }

    private function crearEstudiante(): Estudiante
    {
        $user = User::factory()->create([
            'role' => 'estu',
            'name' => 'Alumno',
            'apellido' => 'Prueba',
        ]);

        return Estudiante::create([
            'codigo' => 'EST-'.uniqid(),
            'user_id' => $user->id,
        ]);
    }

    public function test_docente_puede_ver_su_panel(): void
    {
        $docente = $this->crearDocente();
        $this->crearCurso($docente);

        Livewire::actingAs($docente)
            ->test('docente.dashboard-docente')
            ->assertStatus(200)
            ->assertSee('Panel del Docente');
    }

    public function test_docente_solo_ve_sus_propios_cursos(): void
    {
        $docente = $this->crearDocente();
        $otroDocente = $this->crearDocente();

        $this->crearCurso($docente, ['nombre' => 'Curso Propio']);
        $this->crearCurso($otroDocente, ['nombre' => 'Curso Ajeno']);

        Livewire::actingAs($docente)
            ->test('docente.mis-cursos-docente')
            ->assertSee('Curso Propio')
            ->assertDontSee('Curso Ajeno');
    }

    public function test_docente_puede_editar_su_curso(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);

        Livewire::actingAs($docente)
            ->test('docente.editar-curso-docente', ['curso' => $curso])
            ->set('nombre', 'Curso Actualizado')
            ->call('actualizarCurso')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cursos', [
            'codigo' => $curso->codigo,
            'nombre' => 'Curso Actualizado',
        ]);
    }

    public function test_docente_no_puede_editar_curso_ajeno(): void
    {
        $docente = $this->crearDocente();
        $otroDocente = $this->crearDocente();
        $cursoAjeno = $this->crearCurso($otroDocente);

        Livewire::actingAs($docente)
            ->test('docente.editar-curso-docente', ['curso' => $cursoAjeno])
            ->assertForbidden();
    }

    public function test_docente_puede_alternar_comunidad_de_su_curso(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente, ['comunidad_habilitada' => true]);

        Livewire::actingAs($docente)
            ->test('docente.editar-curso-docente', ['curso' => $curso])
            ->set('comunidad_habilitada', false)
            ->call('actualizarCurso')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('cursos', [
            'codigo' => $curso->codigo,
            'comunidad_habilitada' => false,
        ]);
    }

    public function test_docente_puede_gestionar_recursos_de_su_curso(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);

        Livewire::actingAs($docente)
            ->test('docente.gestionar-recursos-docente', ['curso' => $curso])
            ->call('abrirModal')
            ->set('titulo', 'Guía de estudio')
            ->set('tipo', 'guia')
            ->set('version', '1.0')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('curso_recursos', [
            'curso_id' => $curso->codigo,
            'titulo' => 'Guía de estudio',
        ]);
    }

    public function test_docente_no_puede_gestionar_recursos_de_curso_ajeno(): void
    {
        $docente = $this->crearDocente();
        $otroDocente = $this->crearDocente();
        $cursoAjeno = $this->crearCurso($otroDocente);

        Livewire::actingAs($docente)
            ->test('docente.gestionar-recursos-docente', ['curso' => $cursoAjeno])
            ->assertForbidden();
    }

    public function test_docente_puede_registrar_asistencia(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);
        $estudiante = $this->crearEstudiante();
        $curso->estudiantes()->attach($estudiante->codigo, ['estado' => 'inscrito']);

        Livewire::actingAs($docente)
            ->test('docente.gestionar-asistencias', ['curso' => $curso])
            ->set('fecha', now()->format('Y-m-d'))
            ->set('estados.'.$estudiante->codigo, 'presente')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('asistencias', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado' => 'presente',
        ]);
    }

    public function test_admin_puede_registrar_asistencia_de_cualquier_curso(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);
        $estudiante = $this->crearEstudiante();
        $curso->estudiantes()->attach($estudiante->codigo, ['estado' => 'inscrito']);

        Livewire::actingAs($admin)
            ->test('admin.asistencias.gestionar-asistencias')
            ->set('cursoSeleccionado', $curso->codigo)
            ->set('fecha', now()->format('Y-m-d'))
            ->set('estados.'.$estudiante->codigo, 'tarde')
            ->call('guardar')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('asistencias', [
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado' => 'tarde',
        ]);
    }

    public function test_resumen_se_actualiza_al_cambiar_la_fecha(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);
        $estudianteA = $this->crearEstudiante();
        $estudianteB = $this->crearEstudiante();
        $curso->estudiantes()->attach($estudianteA->codigo, ['estado' => 'inscrito']);
        $curso->estudiantes()->attach($estudianteB->codigo, ['estado' => 'inscrito']);

        $hoy = now()->format('Y-m-d');
        $ayer = now()->subDay()->format('Y-m-d');

        Asistencia::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudianteA->codigo,
            'fecha' => $hoy,
            'estado' => 'ausente',
            'registrado_por' => $docente->id,
        ]);

        Asistencia::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudianteB->codigo,
            'fecha' => $ayer,
            'estado' => 'tarde',
            'registrado_por' => $docente->id,
        ]);

        $componente = Livewire::actingAs($docente)
            ->test('docente.gestionar-asistencias', ['curso' => $curso])
            ->set('fecha', $hoy);

        $this->assertSame('ausente', $componente->get('estados')[$estudianteA->codigo]);
        $this->assertSame('presente', $componente->get('estados')[$estudianteB->codigo]);
        $this->assertSame(1, $componente->viewData('resumen')['ausente']);
        $this->assertSame(1, $componente->viewData('resumen')['presente']);

        $componente->set('fecha', $ayer);

        $this->assertSame('presente', $componente->get('estados')[$estudianteA->codigo]);
        $this->assertSame('tarde', $componente->get('estados')[$estudianteB->codigo]);
        $this->assertSame(1, $componente->viewData('resumen')['tarde']);
        $this->assertSame(1, $componente->viewData('resumen')['presente']);
    }

    public function test_estudiante_no_puede_acceder_al_panel_docente(): void
    {
        $estudiante = User::factory()->create(['role' => 'estu']);

        $this->actingAs($estudiante)
            ->get(route('docente.dashboard'))
            ->assertForbidden();
    }

    public function test_docente_puede_exportar_resumen_de_asistencias(): void
    {
        Excel::fake();

        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);
        $estudiante = $this->crearEstudiante();
        $curso->estudiantes()->attach($estudiante->codigo, ['estado' => 'inscrito']);

        Asistencia::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'fecha' => now()->format('Y-m-d'),
            'estado' => 'presente',
            'registrado_por' => $docente->id,
        ]);

        Livewire::actingAs($docente)
            ->test('docente.gestionar-asistencias', ['curso' => $curso])
            ->call('exportarExcel')
            ->assertHasNoErrors();

        Excel::assertDownloaded(
            sprintf('asistencias-%s-%s.xlsx', $curso->codigo, now()->format('Y-m-d'))
        );
    }

    public function test_admin_puede_exportar_resumen_de_asistencias(): void
    {
        Excel::fake();

        $admin = User::factory()->create(['role' => 'admin']);
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);
        $estudiante = $this->crearEstudiante();
        $curso->estudiantes()->attach($estudiante->codigo, ['estado' => 'inscrito']);

        Asistencia::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'fecha' => now()->format('Y-m-d'),
            'estado' => 'tarde',
            'registrado_por' => $admin->id,
        ]);

        Livewire::actingAs($admin)
            ->test('admin.asistencias.gestionar-asistencias')
            ->set('cursoSeleccionado', $curso->codigo)
            ->call('exportarExcel')
            ->assertHasNoErrors();

        Excel::assertDownloaded(
            sprintf('asistencias-%s-%s.xlsx', $curso->codigo, now()->format('Y-m-d'))
        );
    }

    public function test_modelo_asistencia_tiene_relaciones(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);
        $estudiante = $this->crearEstudiante();

        $asistencia = Asistencia::create([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'fecha' => now()->format('Y-m-d'),
            'estado' => 'justificado',
            'registrado_por' => $docente->id,
        ]);

        $this->assertEquals($curso->codigo, $asistencia->curso->codigo);
        $this->assertEquals($estudiante->codigo, $asistencia->estudiante->codigo);
        $this->assertEquals('Justificado', $asistencia->estado_etiqueta);
    }

    public function test_las_acciones_rapidas_del_panel_apuntan_a_destinos_distintos(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);

        Livewire::actingAs($docente)
            ->test('docente.dashboard-docente')
            ->assertSee(route('docente.cursos.index'), false)
            ->assertSee(route('docente.cursos.recursos', $curso), false)
            ->assertSee(route('docente.cursos.asistencias', $curso), false);
    }

    public function test_las_acciones_rapidas_no_apuntan_al_indice_cuando_hay_curso(): void
    {
        $docente = $this->crearDocente();
        $curso = $this->crearCurso($docente);

        $html = Livewire::actingAs($docente)
            ->test('docente.dashboard-docente')
            ->html();

        $this->assertSame(
            1,
            substr_count($html, 'href="'.route('docente.cursos.index').'"'),
            'Solo la acción "Mis Cursos" debe enlazar al índice de cursos.'
        );
    }

    public function test_el_menu_del_docente_no_muestra_el_enlace_dashboard(): void
    {
        $docente = $this->crearDocente();

        $this->actingAs($docente)
            ->get(route('docente.dashboard'))
            ->assertOk()
            ->assertSee('Panel Docente')
            ->assertDontSee('>'.PHP_EOL.'                            Dashboard'.PHP_EOL.'                        </x-nav-link>', false);
    }

    public function test_el_menu_del_estudiante_si_muestra_el_enlace_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'estu']);
        Estudiante::create([
            'codigo' => 'EST-'.uniqid(),
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->get(route('estudiante.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard');
    }
}
