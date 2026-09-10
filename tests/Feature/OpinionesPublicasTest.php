<?php

namespace Tests\Feature;

use App\Livewire\Opiniones;
use App\Models\Administrador;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class OpinionesPublicasTest extends TestCase
{
    use RefreshDatabase;

    private function crearCursoConOpinion(string $nombreEstudiante, string $opinion, int $rating): void
    {
        $admin = Administrador::create([
            'user_id' => User::factory()->create(['role' => 'admin'])->id,
            'departamento' => 'General',
            'cargo' => 'Administrador',
            'super_admin' => true,
        ]);

        $curso = Curso::create([
            'codigo' => 'CURSO-'.uniqid(),
            'nombre' => 'Curso de Prueba',
            'slug' => 'curso-de-prueba-'.uniqid(),
            'descripcion' => 'Descripción del curso de prueba',
            'cronograma' => 'Lunes a viernes',
            'requisitos' => 'Ninguno',
            'cupo_total' => 30,
            'cupo_disponible' => 30,
            'publicado' => true,
            'creado_por_admin' => $admin->idAdmin,
        ]);

        $user = User::factory()->create([
            'name' => $nombreEstudiante,
            'apellido' => 'Apellido',
            'role' => 'estu',
        ]);

        $estudiante = Estudiante::create([
            'codigo' => 'EST-'.uniqid(),
            'user_id' => $user->id,
            'activo' => true,
        ]);

        $estudiante->cursos()->attach($curso->codigo, [
            'estado' => 'completado',
            'rating_estudiante' => $rating,
            'opinion_estudiante' => $opinion,
        ]);
    }

    public function test_pagina_publica_de_opiniones_es_accesible_sin_autenticacion(): void
    {
        $this->get(route('opiniones.index'))->assertOk();
    }

    public function test_muestra_las_opiniones_de_los_estudiantes(): void
    {
        $this->crearCursoConOpinion('Ana', 'Excelente curso, muy recomendado', 5);

        Livewire::test(Opiniones::class)
            ->assertSee('Ana')
            ->assertSee('Excelente curso, muy recomendado')
            ->assertSee('Curso de Prueba');
    }

    public function test_filtra_opiniones_por_rating(): void
    {
        $this->crearCursoConOpinion('Ana', 'Excelente curso', 5);
        $this->crearCursoConOpinion('Beto', 'Curso regular', 2);

        Livewire::test(Opiniones::class)
            ->call('setRatingFilter', 5)
            ->assertSee('Ana')
            ->assertDontSee('Beto');
    }

    public function test_busqueda_filtra_opiniones_por_texto(): void
    {
        $this->crearCursoConOpinion('Ana', 'Excelente curso de programación', 5);
        $this->crearCursoConOpinion('Beto', 'Curso de cocina básica', 4);

        Livewire::test(Opiniones::class)
            ->set('search', 'programación')
            ->assertSee('Ana')
            ->assertDontSee('Beto');
    }
}
