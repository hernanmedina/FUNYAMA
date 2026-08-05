<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Database\Seeder;
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
                'documento_ID' => '111222333444',
                'email' => 'admin@funyama.com',
                'password' => Hash::make('admin123456'),
                'role' => 'admin',
                'telefono' => '9999999999',
            ]
        );

        // ✅ Crear administrador vinculado
        Administrador::firstOrCreate(
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

        // ✅ Crear 10 usuarios estudiantes
        for ($i = 1; $i <= 10; $i++) {
            $user = User::firstOrCreate(
                ['email' => "estudiante{$i}@funyama.com"],
                [
                    'name' => "Estudiante {$i}",
                    'apellido' => 'Apellido',
                    'documento_ID' => "12345678{$i}",
                    'email' => "estudiante{$i}@funyama.com",
                    'password' => Hash::make('estudiante1234'),
                    'role' => 'estu',
                    'telefono' => '1234567890',
                ]
            );

            // ✅ Crear estudiante vinculado
            $codigoEstudiante = 'EST-'.date('Y').'-'.str_pad($i, 3, '0', STR_PAD_LEFT);
            Estudiante::firstOrCreate(
                ['codigo' => $codigoEstudiante],
                [
                    'codigo' => $codigoEstudiante,
                    'user_id' => $user->id,
                    'fecha_nacimiento' => now()->subYears(20 + $i),
                    'genero' => 'masculino',
                    'nivel_educativo' => 'Universitario',
                    'intereses' => 'Desarrollo, Programación',
                    'fecha_registro' => now(),
                    'activo' => true,
                ]
            );
        }

        $this->command->info('✅ 10 estudiantes de prueba creados exitosamente.');
    }
}
