<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Evento;
use App\Models\Administrador;
use Illuminate\Database\Seeder;

class EventosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Administrador::first();

        $eventos = [
            [
                'titulo' => 'Meetup: Desarrollo Web Moderno',
                'slug' => 'meetup-desarrollo-web-moderno',
                'descripcion' => 'Encuentro presencial para desarrolladores donde compartiremos las últimas tendencias en desarrollo web.',
                'contenido' => 'Charla sobre React 18, Next.js 13, y las nuevas features de JavaScript. Networking con otros desarrolladores.',
                'fecha' => now()->addDays(5)->format('Y-m-d'),
                'hora_inicio' => '18:00',
                'hora_fin' => '21:00',
                'ubicacion' => 'Coworking Central',
                'direccion' => 'Av. Principal 123, Ciudad',
                'ciudad' => 'Bogotá',
                'imagen' => 'eventos/meetup-web.jpg',
                'cupo_maximo' => 50,
                'inscritos_actual' => 35,
                'costo' => 0,
                'tipo_evento' => 'presencial',
                'publicado' => true,
                'destacado' => true,
                'creado_por_admin' => $admin->idAdmin,
            ],
            [
                'titulo' => 'Workshop: Introducción a Docker',
                'slug' => 'workshop-introduccion-docker',
                'descripcion' => 'Taller práctico para aprender los fundamentos de Docker y containers.',
                'contenido' => 'Instalación, comandos básicos, Dockerfile, docker-compose, despliegue de aplicaciones.',
                'fecha' => now()->addDays(12)->format('Y-m-d'),
                'hora_inicio' => '10:00',
                'hora_fin' => '14:00',
                'ubicacion' => 'Laboratorio de Computación',
                'direccion' => 'Calle Tecno 456',
                'ciudad' => 'Medellín',
                'imagen' => 'eventos/workshop-docker.jpg',
                'cupo_maximo' => 25,
                'inscritos_actual' => 12,
                'costo' => 50.00,
                'tipo_evento' => 'presencial',
                'publicado' => true,
                'destacado' => false,
                'creado_por_admin' => $admin->idAdmin,
            ]
        ];

        foreach ($eventos as $evento) {
            Evento::updateOrCreate(
                ['slug' => $evento['slug']],
                $evento
            );
        }

        $this->command->info('✅ 2 eventos de prueba creados exitosamente.');
    }
}
