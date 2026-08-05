<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuario Administrador
        $adminUser = User::updateOrCreate(
            ['email' => 'admin@funyama.com'],
            [
                'name' => 'Admin',
                'apellido' => 'FUNYAMA',
                'documento_ID' => '1118223344',
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

        $this->command->info('✅ Administrador creado exitosamente: admin@funyama.com');
    }
}
