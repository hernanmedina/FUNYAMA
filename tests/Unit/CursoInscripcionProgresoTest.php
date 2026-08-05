<?php

namespace Tests\Unit;

use App\Livewire\Admin\Cursos\MostrarCurso;
use App\Models\Curso;
use PHPUnit\Framework\TestCase;

class CursoInscripcionProgresoTest extends TestCase
{
    public function test_esta_inscrito_es_false_por_defecto(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionProperty($component, 'estaInscrito');
        $reflection->setAccessible(true);

        $this->assertFalse($reflection->getValue($component));
    }

    public function test_temario_progreso_es_array_vacio_por_defecto(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionProperty($component, 'temarioProgreso');
        $reflection->setAccessible(true);

        $this->assertIsArray($reflection->getValue($component));
        $this->assertEmpty($reflection->getValue($component));
    }

    public function test_normalizar_progreso_con_string_json(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'normalizarProgreso');
        $reflection->setAccessible(true);

        $json = '{"0":true,"1":false,"2":true}';
        $resultado = $reflection->invoke($component, $json);

        $this->assertIsArray($resultado);
        $this->assertTrue($resultado[0]);
        $this->assertFalse($resultado[1]);
        $this->assertTrue($resultado[2]);
    }

    public function test_normalizar_progreso_con_array(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'normalizarProgreso');
        $reflection->setAccessible(true);

        $array = [true, false, true];
        $resultado = $reflection->invoke($component, $array);

        $this->assertIsArray($resultado);
        $this->assertCount(3, $resultado);
    }

    public function test_normalizar_progreso_con_string_invalido_retorna_array_vacio(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'normalizarProgreso');
        $reflection->setAccessible(true);

        $resultado = $reflection->invoke($component, 'texto-invalido');

        $this->assertIsArray($resultado);
        $this->assertEmpty($resultado);
    }

    public function test_calcular_progreso_con_todos_completados(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'calcularProgreso');
        $reflection->setAccessible(true);

        $progreso = [true, true, true, true];
        $total = 4;

        $this->assertSame(100.0, $reflection->invoke($component, $progreso, $total));
    }

    public function test_calcular_progreso_con_ninguno_completado(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'calcularProgreso');
        $reflection->setAccessible(true);

        $progreso = [false, false, false];
        $total = 3;

        $this->assertSame(0.0, $reflection->invoke($component, $progreso, $total));
    }

    public function test_calcular_progreso_con_cero_items(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'calcularProgreso');
        $reflection->setAccessible(true);

        $progreso = [];
        $total = 0;

        $this->assertSame(0.0, $reflection->invoke($component, $progreso, $total));
    }

    public function test_calcular_progreso_con_mitad_completados(): void
    {
        $component = new MostrarCurso;
        $reflection = new \ReflectionMethod($component, 'calcularProgreso');
        $reflection->setAccessible(true);

        $progreso = [true, false, true, false];
        $total = 4;

        $this->assertSame(50.0, $reflection->invoke($component, $progreso, $total));
    }

    public function test_temario_items_se_obtienen_desde_el_modelo(): void
    {
        $curso = new Curso([
            'temario' => "Módulo 1: Introducción\nMódulo 2: Conceptos Avanzados\nMódulo 3: Proyecto Final",
        ]);

        $items = $curso->temario_items;

        $this->assertCount(3, $items);
        $this->assertEquals('Módulo 1: Introducción', $items[0]);
        $this->assertEquals('Módulo 2: Conceptos Avanzados', $items[1]);
        $this->assertEquals('Módulo 3: Proyecto Final', $items[2]);
    }

    public function test_temario_items_con_texto_vacio_retorna_array_vacio(): void
    {
        $curso = new Curso([
            'temario' => '',
        ]);

        $items = $curso->temario_items;

        $this->assertIsArray($items);
        $this->assertEmpty($items);
    }

    public function test_inscritos_actuales_se_calcula_a_partir_de_los_cupos(): void
    {
        $curso = new Curso([
            'cupo_total' => 10,
            'cupo_disponible' => 6,
        ]);

        $this->assertSame(4, $curso->inscritos_actuales);
    }

    public function test_inscritos_actuales_no_puede_ser_negativo(): void
    {
        $curso = new Curso([
            'cupo_total' => 5,
            'cupo_disponible' => 8,
        ]);

        $this->assertSame(0, $curso->inscritos_actuales);
    }
}
