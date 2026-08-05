<?php

namespace Database\Seeders;

use App\Models\Solicitud;
use Illuminate\Database\Seeder;

class SolicitudesSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 10; $i++) {
            Solicitud::updateOrCreate(
                ['asunto' => "Solicitud de prueba {$i}"],
                [
                    'tipo' => 'informacion',
                    'asunto' => "Solicitud de prueba {$i}",
                    'mensaje' => "Este es un mensaje de prueba para la solicitud número {$i}.",
                    'estado' => 'pendiente',
                ]
            );
        }

        $this->command->info('✅ 10 solicitudes de prueba creadas exitosamente.');
    }
}
