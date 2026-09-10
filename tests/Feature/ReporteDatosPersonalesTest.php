<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\User;
use App\Services\ReporteExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReporteDatosPersonalesTest extends TestCase
{
    use RefreshDatabase;

    private function crearEstudianteConDatos(): Estudiante
    {
        $user = User::factory()->create([
            'name' => 'Juan',
            'apellido' => 'Pérez',
            'documento_ID' => 'V-12345678',
            'email' => 'juan.perez@example.com',
            'telefono' => '04141234567',
            'direccion' => 'Av. Principal, Caracas',
            'role' => 'estu',
        ]);

        $estudiante = Estudiante::create([
            'codigo' => 'EST-001',
            'user_id' => $user->id,
            'fecha_nacimiento' => '1995-05-20',
            'genero' => 'masculino',
            'nivel_educativo' => 'Universitario',
            'intereses' => 'Programación, diseño',
            'fecha_registro' => now(),
            'activo' => true,
        ]);

        $curso = Curso::create([
            'codigo' => 'CUR-001',
            'nombre' => 'Curso de prueba',
            'slug' => 'curso-de-prueba',
            'descripcion' => 'Descripción del curso de prueba.',
            'cronograma' => 'Cronograma de prueba',
            'requisitos' => 'Requisitos de prueba',
            'cupo_total' => 20,
            'cupo_disponible' => 20,
            'precio_regular' => 100,
            'nivel' => 'principiante',
            'publicado' => true,
        ]);

        $estudiante->cursos()->attach($curso->codigo, [
            'estado' => 'inscrito',
            'estado_pago' => 'pendiente',
            'fecha_inscripcion' => now(),
        ]);

        return $estudiante;
    }

    public function test_exporta_datos_personales_de_todos_los_estudiantes(): void
    {
        $this->crearEstudianteConDatos();

        $service = app(ReporteExportService::class);
        $reflection = new \ReflectionClass($service);
        $metodo = $reflection->getMethod('obtenerDatosEstudiantesExportables');
        $metodo->setAccessible(true);

        $datos = $metodo->invoke($service, 'datos_personales', null);

        $this->assertCount(1, $datos);
        $fila = $datos[0];

        $this->assertSame('EST-001', $fila['codigo']);
        $this->assertSame('Juan Pérez', $fila['nombre_completo']);
        $this->assertSame('V-12345678', $fila['documento']);
        $this->assertSame('20/05/1995', $fila['fecha_nacimiento']);
        $this->assertSame('Masculino', $fila['genero']);
        $this->assertSame('juan.perez@example.com', $fila['email']);
        $this->assertSame('04141234567', $fila['telefono']);
        $this->assertSame('Av. Principal, Caracas', $fila['direccion']);
        $this->assertSame('Universitario', $fila['nivel_educativo']);
        $this->assertSame('Programación, diseño', $fila['intereses']);
        $this->assertSame('Curso de prueba', $fila['cursos']);
        $this->assertSame('Sí', $fila['activo']);
    }

    public function test_las_cabeceras_de_datos_personales_incluyen_los_campos_esperados(): void
    {
        $service = app(ReporteExportService::class);
        $reflection = new \ReflectionClass($service);
        $metodo = $reflection->getMethod('obtenerCabecerasExport');
        $metodo->setAccessible(true);

        $cabeceras = $metodo->invoke($service, 'estudiantes', 'datos_personales');

        $this->assertSame('Nombre completo', $cabeceras['nombre_completo']);
        $this->assertSame('Cédula / Documento', $cabeceras['documento']);
        $this->assertSame('Fecha de nacimiento', $cabeceras['fecha_nacimiento']);
        $this->assertSame('Género', $cabeceras['genero']);
        $this->assertSame('Teléfono', $cabeceras['telefono']);
        $this->assertSame('Dirección', $cabeceras['direccion']);
    }
}
