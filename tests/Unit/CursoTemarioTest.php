<?php

namespace Tests\Unit;

use App\Livewire\Admin\Cursos\MostrarCurso;
use App\Models\Curso;
use PHPUnit\Framework\TestCase;

class CursoTemarioTest extends TestCase
{
    public function test_it_parses_temario_items_from_multiline_text(): void
    {
        $curso = new Curso([
            'temario' => "Introducción\n\nUnidad 1\nUnidad 2",
        ]);

        $this->assertSame([
            'Introducción',
            'Unidad 1',
            'Unidad 2',
        ], $curso->temario_items);
    }

    public function test_it_calculates_progress_percentage_for_completed_items(): void
    {
        $component = new MostrarCurso();
        $reflection = new \ReflectionMethod($component, 'calcularProgreso');
        $reflection->setAccessible(true);

        $progreso = [0 => true, 1 => false, 2 => true];
        $total = 3;

        $this->assertSame(66.67, round($reflection->invoke($component, $progreso, $total), 2));
    }
}
