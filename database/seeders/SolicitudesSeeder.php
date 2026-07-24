<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Solicitud;
use App\Models\User;
use App\Models\Administrador;
use Illuminate\Database\Seeder;

class SolicitudesSeeder extends Seeder
{
    public function run(): void
    {
        $estudiante = User::where('email', 'estudiante@funyama.com')->first();
        $admin = Administrador::first();

        $solicitudes = [
            [
                'tipo' => 'informacion',
                'asunto' => 'Consulta sobre curso de Laravel',
                'mensaje' => 'Buen día, me gustaría saber si el curso de Laravel incluye proyecto final y si hay descuentos para estudiantes.',
                'telefono' => '+573001234567',
                'email_contacto' => 'estudiante@funyama.com',
                'estado' => 'resuelta',
                'respuesta' => 'Hola, sí el curso incluye proyecto final guiado. Tenemos 20% de descuento para estudiantes con carnet vigente.',
                'fecha_respuesta' => now()->subDays(1),
                'user_id' => $estudiante->id,
                'atendido_por_admin' => $admin->idAdmin,
            ],
            [
                'tipo' => 'inscripcion',
                'asunto' => 'Problema con inscripción a curso',
                'mensaje' => 'No puedo completar mi inscripción al curso de React, me aparece error en el pago.',
                'telefono' => '+573005678901',
                'estado' => 'en_proceso',
                'user_id' => $estudiante->id,
            ],
            [
                'tipo' => 'sugerencia',
                'asunto' => 'Sugerencia para nuevos cursos',
                'mensaje' => 'Sería excelente que agreguen un curso sobre Vue.js y otro sobre Node.js avanzado.',
                'estado' => 'pendiente',
                'user_id' => null,
                'email_contacto' => 'sugerencia@email.com',
            ]
        ];

        foreach ($solicitudes as $solicitud) {
            // Para solicitudes no usamos slug, podemos usar una combinación única
            Solicitud::updateOrCreate(
                [
                    'asunto' => $solicitud['asunto'],
                    'user_id' => $solicitud['user_id'] ?? null
                ],
                $solicitud
            );
        }

        $this->command->info('✅ 3 solicitudes de prueba creadas exitosamente.');
    }
}
