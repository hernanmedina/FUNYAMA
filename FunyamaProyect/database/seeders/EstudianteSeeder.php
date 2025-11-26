<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Curso;
use App\Models\Certificado;
use Illuminate\Support\Facades\Hash;


class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ CORRECTO - Crear usuario estudiante
        $user = User::firstOrCreate(
            ['email' => 'estudiante2@funyama.com'],
            [
                'name' => 'Juan',
                'apellido' => 'Pérez',
                'email' => 'estudiante2@funyama.com',
                'password' => Hash::make('estudiante1234'),
                'role' => 'estu',
                'telefono' => '1234567890',
                'direccion' => 'Calle Principal 123',
            ]
        );

        // ✅ CORRECTO - Crear estudiante vinculado
        $estudiante = Estudiante::firstOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'matricula' => 'EST000001',
                'fecha_nacimiento' => now()->subYears(25),
                'genero' => 'masculino',
                'nivel_educativo' => 'Universitario',
                'intereses' => 'Desarrollo web, bases de datos, programación',
                'fecha_registro' => now(),
                'activo' => true,
            ]
        );

        // ⚠️ AJUSTE NECESARIO - Asegurar que existe un admin para la relación
        // VERIFICAR ADMINISTRADOR - Agregar esto antes de crear el curso
        // $admin = \App\Models\Administrador::first();
        // if (!$admin) {
        //     // Si no existe administrador, crear uno temporal
        //     $admin = \App\Models\Administrador::create([
        //         'nombre' => 'Admin FunYama',
        //         'email' => 'admin@funyama.com',
        //         'password' => Hash::make('admin123'),
        //         'role' => 'admin',
        //         'activo' => true,
        //         // ... otros campos según tu migración de administradores
        //     ]);
        //     $this->command->info('✅ Administrador temporal creado: admin@funyama.com / admin123');
        // }

        // ✅ CORRECTO - Crear curso
        $curso = Curso::firstOrCreate(
            ['nombre' => 'Laravel Avanzado'],
            [
                'nombre' => 'Laravel Avanzado',
                'slug' => 'laravel-avanzado',
                'descripcion' => 'Curso avanzado de Laravel con mejores prácticas',
                'cronograma' => 'Lunes a viernes, 2 horas diarias',
                'requisitos' => 'Conocimientos básicos de PHP',
                'objetivos' => 'Dominar patrones de diseño y arquitectura en Laravel',
                'duracion_horas' => 40,
                'duracion_texto' => '4 semanas',
                'precio_regular' => 99.99,
                'precio_descuento' => 79.99,
                'nivel' => 'Avanzado',
                'publicado' => true,
                'destacado' => true,
                'cupo_total' => 30,
                'cupo_disponible' => 25,
                'creado_por_admin' => 1, // $admin->idAdmin, ✅ Usar idAdmin del administrador
            ]
        );

        // ✅ CORRECTO - Usar el método existente
        $numeroCertificado = Certificado::generarNumeroCertificado($estudiante->idEstudiante, $curso->idCurso);
        
        // ✅ CORRECTO - Crear certificado
        Certificado::firstOrCreate(
            [
                'estudiante_id' => $estudiante->idEstudiante, 
                'curso_id' => $curso->idCurso
            ],
            [
                'estudiante_id' => $estudiante->idEstudiante,
                'curso_id' => $curso->idCurso,
                'numero_certificado' => $numeroCertificado,
                'fecha_emision' => now()->subDays(10),
                'calificacion_final' => 9.5,
                'archivo_path' => 'certificados/est-' . $estudiante->idEstudiante . '-curso-' . $curso->idCurso . '.pdf',
                'descargas' => 2,
                'ultima_descarga' => now()->subDays(5),
            ]
        );

        $this->command->info('Seeder de estudiante ejecutado correctamente.');
        $this->command->info('Email: estudiante2@funyama.com');
        $this->command->info('Contraseña: estudiante1234');
    }
}