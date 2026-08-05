<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Evento;
use Illuminate\Database\Seeder;

class EventosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Administrador::first();

        for ($i = 1; $i <= 10; $i++) {
            Evento::updateOrCreate(
                ['slug' => "evento-{$i}"],
                [
                    'titulo' => "Evento de prueba {$i}",
                    'slug' => "evento-{$i}",
                    'descripcion' => "Descripción del evento de prueba número {$i}",
                    'fecha' => now()->addDays($i)->format('Y-m-d'),
                    'hora_inicio' => '10:00',
                    'ubicacion' => 'Auditorio Principal',
                    'creado_por_admin' => $admin->idAdmin,
                ]
            );
        }

        $this->command->info('✅ 10 eventos de prueba creados exitosamente.');
    }
}
