<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Conferencia;
use App\Models\Administrador;
use Illuminate\Database\Seeder;

class ConferenciasSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Administrador::first();

        $conferencias = [
            [
                'titulo' => 'El Futuro de la Inteligencia Artificial',
                'slug' => 'futuro-inteligencia-artificial',
                'descripcion' => 'Conferencia sobre las últimas tendencias y aplicaciones prácticas de IA en el mundo real.',
                'temario' => '1. Estado actual de la IA | 2. ChatGPT y modelos de lenguaje | 3. IA aplicada a negocios | 4. Futuras direcciones',
                'fecha' => now()->addDays(3)->format('Y-m-d'),
                'hora_inicio' => '19:00',
                'hora_fin' => '20:30',
                'ponente' => 'Dra. Elena Martínez',
                'bio_ponente' => 'PhD en Ciencias de la Computación, 10 años de experiencia en IA, investigadora principal en TechLab.',
                'modalidad' => 'virtual',
                'enlace' => 'https://meet.google.com/abc-defg-hij',
                'cupo_maximo' => 100,
                'inscritos_actual' => 67,
                'costo' => 0,
                'nivel' => 'general',
                'publicado' => true,
                'destacado' => true,
                'creado_por_admin' => $admin->idAdmin,
            ],
            [
                'titulo' => 'Seguridad Web en 2024',
                'slug' => 'seguridad-web-2024',
                'descripcion' => 'Aprende las mejores prácticas de seguridad para proteger tus aplicaciones web.',
                'temario' => 'OWASP Top 10, autenticación segura, protección de datos, herramientas de seguridad',
                'fecha' => now()->addDays(8)->format('Y-m-d'),
                'hora_inicio' => '17:00',
                'hora_fin' => '18:30',
                'ponente' => 'Ing. Carlos Security',
                'bio_ponente' => 'Especialista en ciberseguridad con 8 años de experiencia, pentester certificado.',
                'modalidad' => 'virtual',
                'enlace' => 'https://zoom.us/j/123456789',
                'cupo_maximo' => 80,
                'inscritos_actual' => 45,
                'costo' => 25.00,
                'nivel' => 'avanzado',
                'publicado' => true,
                'destacado' => false,
                'creado_por_admin' => $admin->idAdmin,
            ]
        ];

        foreach ($conferencias as $conferencia) {
            Conferencia::updateOrCreate(
                ['slug' => $conferencia['slug']],
                $conferencia
            );
        }

        $this->command->info('✅ 2 conferencias de prueba creadas exitosamente.');
    }
}
