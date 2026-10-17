<?php

namespace Tests\Feature;

use App\Livewire\Admin\GestionarCertificados;
use App\Models\Administrador;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\TestCase;

class GestionarCertificadosTest extends TestCase
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
            'name' => 'Estudiante',
            'apellido' => 'Prueba',
            'role' => 'estu',
        ]);

        return Estudiante::create([
            'codigo' => 'EST-'.fake()->unique()->numerify('####'),
            'user_id' => $user->id,
            'fecha_registro' => now(),
            'activo' => true,
        ]);
    }

    private function crearCurso(): Curso
    {
        return Curso::create([
            'codigo' => 'CUR-'.fake()->unique()->numerify('####'),
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba-'.fake()->unique()->numerify('####'),
            'descripcion' => 'Descripción del curso de prueba para certificados.',
            'cronograma' => 'Cronograma del curso de prueba.',
            'requisitos' => 'Requisitos del curso de prueba.',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);
    }

    private function matricular(Estudiante $estudiante, Curso $curso, string $estado, string $estadoPago): void
    {
        DB::table('curso_estudiante')->insert([
            'curso_id' => $curso->codigo,
            'estudiante_id' => $estudiante->codigo,
            'estado' => $estado,
            'estado_pago' => $estadoPago,
            'pago_realizado' => $estadoPago === 'completo' ? 100 : 0,
            'fecha_inscripcion' => now(),
            'fecha_completado' => $estado === 'completado' ? now() : null,
            'progreso' => $estado === 'completado' ? 100 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function test_muestra_estudiantes_con_curso_finalizado_y_pago_completo_sin_certificado(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $curso = $this->crearCurso();

        $this->matricular($estudiante, $curso, 'completado', 'completo');

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->assertSee('Pendientes de Certificado')
            ->assertSee($estudiante->user->name)
            ->assertSee($curso->nombre)
            ->assertSee('Pago completo');
    }

    public function test_no_muestra_estudiantes_con_pago_pendiente(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $curso = $this->crearCurso();

        $this->matricular($estudiante, $curso, 'completado', 'pendiente');

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->assertViewHas('pendientesCertificado', fn ($pendientes) => $pendientes->isEmpty());
    }

    public function test_no_muestra_estudiantes_con_curso_sin_finalizar(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $curso = $this->crearCurso();

        $this->matricular($estudiante, $curso, 'en_progreso', 'completo');

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->assertViewHas('pendientesCertificado', fn ($pendientes) => $pendientes->isEmpty());
    }

    public function test_no_muestra_estudiantes_que_ya_tienen_certificado(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $curso = $this->crearCurso();

        $this->matricular($estudiante, $curso, 'completado', 'completo');

        Certificado::create([
            'estudiante_id' => $estudiante->codigo,
            'curso_id' => $curso->codigo,
            'numero_certificado' => 'CERT-'.fake()->unique()->numerify('####'),
            'fecha_emision' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->assertViewHas('pendientesCertificado', fn ($pendientes) => $pendientes->isEmpty());
    }

    public function test_el_filtro_de_curso_acota_los_pendientes(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $cursoA = $this->crearCurso();
        $cursoB = $this->crearCurso();

        $this->matricular($estudiante, $cursoA, 'completado', 'completo');
        $this->matricular($estudiante, $cursoB, 'completado', 'completo');

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->set('filtroCurso', $cursoA->codigo)
            ->assertViewHas('pendientesCertificado', function ($pendientes) use ($cursoA) {
                return $pendientes->count() === 1
                    && $pendientes->first()->curso_id === $cursoA->codigo;
            });
    }

    public function test_abrir_modal_para_pendiente_preselecciona_estudiante_y_curso(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $curso = $this->crearCurso();

        $this->matricular($estudiante, $curso, 'completado', 'completo');

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->call('abrirModalCargaPara', $estudiante->codigo, $curso->codigo)
            ->assertSet('mostrarModalCarga', true)
            ->assertSet('estudianteSeleccionado', $estudiante->codigo)
            ->assertSet('cursoSeleccionado', $curso->codigo);
    }

    public function test_al_subir_certificado_el_pendiente_desaparece(): void
    {
        $admin = $this->crearAdmin();
        $estudiante = $this->crearEstudiante();
        $curso = $this->crearCurso();

        $this->matricular($estudiante, $curso, 'completado', 'completo');

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->assertViewHas('pendientesCertificado', fn ($pendientes) => $pendientes->count() === 1);

        Certificado::create([
            'estudiante_id' => $estudiante->codigo,
            'curso_id' => $curso->codigo,
            'numero_certificado' => 'CERT-'.fake()->unique()->numerify('####'),
            'fecha_emision' => now(),
        ]);

        Livewire::actingAs($admin)
            ->test(GestionarCertificados::class)
            ->assertViewHas('pendientesCertificado', fn ($pendientes) => $pendientes->isEmpty());
    }
}
