<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Curso;
use App\Models\Certificado;
use App\Models\Administrador;
use Illuminate\Support\Facades\Hash;


class EstudianteSeeder extends Seeder
{
    public function run(): void
    {
        // ✅ Crear usuario administrador
        $userAdmin = User::firstOrCreate(
            ['email' => 'admin@funyama.com'],
            [
                'name' => 'Admin',
                'apellido' => 'FunYama',
                'email' => 'admin@funyama.com',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'telefono' => '9999999999',
            ]
        );

        // ✅ Crear administrador vinculado
        $admin = Administrador::firstOrCreate(
            ['user_id' => $userAdmin->id],
            [
                'user_id' => $userAdmin->id,
                'departamento' => 'Sistemas',
                'cargo' => 'Administrador General',
                'telefono_contacto' => '9999999999',
                'super_admin' => true,
                'fecha_ingreso' => now(),
            ]
        );

        // ✅ Crear usuario estudiante
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

        // ✅ Crear estudiante vinculado
        $codigoEstudiante = 'EST-' . date('Y') . '-001';
        $estudiante = Estudiante::firstOrCreate(
            ['codigo' => $codigoEstudiante],
            [
                'codigo' => $codigoEstudiante,
                'user_id' => $user->id,
                'fecha_nacimiento' => now()->subYears(25),
                'genero' => 'masculino',
                'nivel_educativo' => 'Universitario',
                'intereses' => 'Desarrollo web, bases de datos, programación',
                'fecha_registro' => now(),
                'activo' => true,
            ]
        );

        // ✅ Crear curso
        $codigoCurso = 'CUR-' . date('Y') . '-001';
        $curso = Curso::firstOrCreate(
            ['codigo' => $codigoCurso],
            [
                'codigo' => $codigoCurso,
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
                'creado_por_admin' => $admin->idAdmin,
            ]
        );

        // ✅ Crear certificado con códigos
        $numeroCertificado = 'CERT-' . date('Y') . '-' . str_pad($codigoEstudiante, 10, '0', STR_PAD_LEFT) . '-' . str_pad($codigoCurso, 10, '0', STR_PAD_LEFT);
        
        Certificado::firstOrCreate(
            [
                'estudiante_id' => $codigoEstudiante, 
                'curso_id' => $codigoCurso
            ],
            [
                'estudiante_id' => $codigoEstudiante,
                'curso_id' => $codigoCurso,
                'numero_certificado' => $numeroCertificado,
                'fecha_emision' => now()->subDays(10),
                'calificacion_final' => 9.5,
                'archivo_path' => 'certificados/est-' . $codigoEstudiante . '-curso-' . $codigoCurso . '.pdf',
                'descargas' => 2,
                'ultima_descarga' => now()->subDays(5),
            ]
        );

        $this->command->info('✅ Seeder de estudiante ejecutado correctamente.');
        $this->command->info('📧 Email Estudiante: estudiante2@funyama.com');
        $this->command->info('🔐 Contraseña Estudiante: estudiante1234');
        $this->command->info('📧 Email Admin: admin@funyama.com');
        $this->command->info('🔐 Contraseña Admin: admin123456');
        $this->command->info('🎓 Código Estudiante: ' . $codigoEstudiante);
        $this->command->info('📚 Código Curso: ' . $codigoCurso);
    }
}