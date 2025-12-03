<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Curso;
use App\Models\Administrador;
use Illuminate\Database\Seeder;

class CursosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Administrador::first();

        if (!$admin) {
            $this->command->error('No se encontró un administrador. Ejecuta primero AdminUserSeeder.');
            return;
        }

        $cursos = [
            [
                'codigo' => 'CUR-' . date('Y') . '-001',
                'nombre' => 'Microsoft Word desde Cero',
                'slug' => 'microsoft-word-desde-cero',
                'descripcion' => 'Aprende a dominar Microsoft Word desde lo más básico hasta herramientas avanzadas para crear documentos profesionales.',
                'cronograma' => 'Semana 1: Fundamentos y edición | Semana 2: Formatos avanzados | Semana 3: Tablas, imágenes y secciones | Semana 4: Documentos profesionales y prácticas',
                'requisitos' => 'Uso básico de computadora. No se requieren conocimientos previos.',
                'objetivos' => 'Crear documentos profesionales, usar estilos, insertar tablas e imágenes, manejar portadas e índices automáticos.',
                'materiales_incluidos' => 'Ejercicios prácticos, plantillas de Word, videos explicativos, certificado',
                'cupo_total' => 30,
                'cupo_disponible' => 20,
                'duracion_horas' => 30,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 120.00,
                'precio_descuento' => 90.00,
                'nivel' => 'principiante',
                'imagen_portada' => 'cursos/word-basico.jpg',
                'publicado' => true,
                'destacado' => true,
                'fecha_inicio' => now()->addDays(7)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],

            [
                'codigo' => 'CUR-' . date('Y') . '-002',
                'nombre' => 'Microsoft Excel Básico a Intermedio',
                'slug' => 'microsoft-excel-basico-intermedio',
                'descripcion' => 'Domina Excel para tareas personales, académicas y laborales. Aprende fórmulas, funciones y análisis de datos.',
                'cronograma' => 'Semana 1: Fundamentos y formatos | Semana 2: Fórmulas y funciones | Semana 3: Tablas dinámicas | Semana 4: Gráficos y análisis de datos',
                'requisitos' => 'Conocer operaciones básicas de computadora.',
                'objetivos' => 'Crear hojas de cálculo eficientes, usar funciones esenciales, realizar análisis con tablas dinámicas, crear gráficos.',
                'materiales_incluidos' => 'Archivos de práctica, ejercicios guiados, videos, certificado',
                'cupo_total' => 25,
                'cupo_disponible' => 12,
                'duracion_horas' => 40,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 150.00,
                'precio_descuento' => 110.00,
                'nivel' => 'intermedio',
                'imagen_portada' => 'cursos/excel-basico.jpg',
                'publicado' => true,
                'destacado' => true,
                'fecha_inicio' => now()->addDays(10)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],

            [
                'codigo' => 'CUR-' . date('Y') . '-003',
                'nombre' => 'Microsoft PowerPoint Profesional',
                'slug' => 'microsoft-powerpoint-profesional',
                'descripcion' => 'Aprende a crear presentaciones atractivas, profesionales y comunicativas utilizando PowerPoint.',
                'cronograma' => 'Semana 1: Fundamentos de diseño | Semana 2: Plantillas y estilos | Semana 3: Animaciones y transiciones | Semana 4: Presentaciones efectivas',
                'requisitos' => 'Conocimientos básicos de informática.',
                'objetivos' => 'Crear diapositivas efectivas, aplicar diseño moderno, integrar multimedia y desarrollar presentaciones profesionales.',
                'materiales_incluidos' => 'Plantillas premium, ejercicios, videos, certificado',
                'cupo_total' => 30,
                'cupo_disponible' => 25,
                'duracion_horas' => 25,
                'duracion_texto' => '3 semanas',
                'precio_regular' => 130.00,
                'nivel' => 'principiante',
                'imagen_portada' => 'cursos/powerpoint.jpg',
                'publicado' => true,
                'destacado' => false,
                'fecha_inicio' => now()->addDays(14)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],

            [
                'codigo' => 'CUR-' . date('Y') . '-004',
                'nombre' => 'Informática Básica para Todos',
                'slug' => 'informatica-basica-para-todos',
                'descripcion' => 'Curso completo para aprender el uso del computador, manejo de archivos, internet, correo electrónico y herramientas esenciales.',
                'cronograma' => 'Semana 1: Partes del computador y sistema operativo | Semana 2: Archivos y carpetas | Semana 3: Internet y seguridad | Semana 4: Herramientas básicas de productividad',
                'requisitos' => 'No se necesitan conocimientos previos.',
                'objetivos' => 'Operar un computador correctamente, navegar en internet de forma segura, usar herramientas básicas y gestionar archivos.',
                'materiales_incluidos' => 'Guías prácticas, videos, ejercicios, certificado',
                'cupo_total' => 20,
                'cupo_disponible' => 18,
                'duracion_horas' => 35,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 100.00,
                'nivel' => 'principiante',
                'imagen_portada' => 'cursos/informatica-basica.jpg',
                'publicado' => true,
                'destacado' => false,
                'fecha_inicio' => now()->addDays(5)->format('Y-m-d'),
                'creado_por_admin' => $admin->idAdmin,
            ],
        ];


        foreach ($cursos as $curso) {
            Curso::updateOrCreate(
                ['codigo' => $curso['codigo']], // Buscar por codigo único
                $curso
            );
        }

        $this->command->info('✅ Cursos de prueba creados/actualizados exitosamente.');

        // Inscribir estudiantes en algunos cursos
        $this->inscribirEstudiantesEnCursos();
    }

    private function inscribirEstudiantesEnCursos()
    {
        $estudiantes = \App\Models\Estudiante::with('user')->get();
        $cursos = \App\Models\Curso::all();

        foreach ($estudiantes as $estudiante) {
            // Inscribir cada estudiante en 1-3 cursos aleatorios
            $cursosAleatorios = $cursos->random(rand(1, 3));

            foreach ($cursosAleatorios as $curso) {
                // Verificar que no esté ya inscrito y que haya cupo
                if (!$estudiante->cursos()->where('curso_id', $curso->codigo)->exists() &&
                    $curso->cupo_disponible > 0) {

                    $estudiante->cursos()->attach($curso->codigo, [
                        'estado' => rand(0, 1) ? 'inscrito' : 'en_progreso',
                        'progreso' => rand(0, 100),
                        'pago_realizado' => $curso->precio_descuento ?? $curso->precio_regular,
                        'estado_pago' => 'completo',
                        'fecha_inscripcion' => now()->subDays(rand(1, 30))
                    ]);

                    // Actualizar cupo disponible
                    $curso->decrement('cupo_disponible');
                }
            }
        }

        $this->command->info('✅ Estudiantes inscritos en cursos exitosamente.');
    }
}
