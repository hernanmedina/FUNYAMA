<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'], 
            [
                'name' => 'Administrador',
                'apellido' => 'Sistema', 
                'password' => Hash::make('admin1234'), 
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
