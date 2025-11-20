<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Administrador;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario Administrador
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@funyama.com'],
            [
                'name' => 'Hernan',
                'apellido' => 'Medina',
                'password' => Hash::make('admin1234'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'telefono' => '+1234567890',
            ]
        );

        // Crear registro en tabla administradores
        if ($adminUser) {
            Administrador::updateOrCreate(
                ['user_id' => $adminUser->id],
                [
                    'departamento' => 'Dirección General',
                    'cargo' => 'Administrador Principal',
                    'telefono_contacto' => '+1234567890',
                    'super_admin' => true,
                    'fecha_ingreso' => now(),
                ]
            );
        }

        // Crear usuario Estudiante
        $estudianteUser = User::updateOrCreate(
            ['email' => 'estudiante@funyama.com'],
            [
                'name' => 'María',
                'apellido' => 'Gonzalez',
                'password' => Hash::make('estudiante1234'),
                'role' => 'estu',
                'email_verified_at' => now(),
                'telefono' => '+0987654321',
            ]
        );

        // Crear registro en tabla estudiantes
        if ($estudianteUser) {
            Estudiante::updateOrCreate(
                ['user_id' => $estudianteUser->id],
                [
                    'matricula' => 'EST' . str_pad($estudianteUser->id, 6, '0', STR_PAD_LEFT),
                    'fecha_nacimiento' => '2000-05-15',
                    'genero' => 'femenino',
                    'nivel_educativo' => 'Universidad',
                    'intereses' => 'Programación, Diseño, Marketing',
                    'fecha_registro' => now(),
                    'activo' => true,
                ]
            );
        }

        // Crear usuario Regular (user)
        User::updateOrCreate(
            ['email' => 'usuario@funyama.com'],
            [
                'name' => 'Carlos',
                'apellido' => 'Lopez',
                'password' => Hash::make('usuario1234'),
                'role' => 'user',
                'email_verified_at' => now(),
                'telefono' => '+1122334455',
            ]
        );

        // Crear más estudiantes de ejemplo
        $estudiantesEjemplo = [
            [
                'name' => 'Ana',
                'apellido' => 'Rodriguez',
                'email' => 'ana@funyama.com',
                'role' => 'estu',
                'estudiante' => [
                    'fecha_nacimiento' => '1999-08-22',
                    'genero' => 'femenino',
                    'nivel_educativo' => 'Secundaria',
                    'intereses' => 'Arte, Música',
                ]
            ],
            [
                'name' => 'Luis',
                'apellido' => 'Martinez',
                'email' => 'luis@funyama.com',
                'role' => 'estu',
                'estudiante' => [
                    'fecha_nacimiento' => '2001-03-10',
                    'genero' => 'masculino',
                    'nivel_educativo' => 'Universidad',
                    'intereses' => 'Deportes, Tecnología',
                ]
            ]
        ];

        foreach ($estudiantesEjemplo as $estudianteData) {
            $user = User::updateOrCreate(
                ['email' => $estudianteData['email']],
                [
                    'name' => $estudianteData['name'],
                    'apellido' => $estudianteData['apellido'],
                    'password' => Hash::make('password123'),
                    'role' => $estudianteData['role'],
                    'email_verified_at' => now(),
                ]
            );

            if ($user) {
                Estudiante::updateOrCreate(
                    ['user_id' => $user->id],
                    array_merge(
                        $estudianteData['estudiante'],
                        [
                            'matricula' => 'EST' . str_pad($user->id, 6, '0', STR_PAD_LEFT),
                            'fecha_registro' => now(),
                            'activo' => true,
                        ]
                    )
                );
            }
        }

        $this->command->info('Usuarios de prueba creados:');
        $this->command->info('- Administrador: admin@funyama.com / admin1234');
        $this->command->info('- Estudiante: estudiante@funyama.com / estudiante1234');
        $this->command->info('- Usuario Regular: usuario@funyama.com / usuario1234');
        $this->command->info('- Estudiantes adicionales: ana@funyama.com / luis@funyama.com (password: password123)');
    }
}
