<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Articulo;
use App\Models\Administrador;
use Illuminate\Database\Seeder;

class ArticulosSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Administrador::first();

        $articulos = [
            [
                'titulo' => 'Por qué aprender Laravel en 2024',
                'slug' => 'por-que-aprender-laravel-2024',
                'resumen' => 'Descubre las razones por las cuales Laravel sigue siendo el framework PHP más popular y por qué deberías aprenderlo este año.',
                'contenido' => 'Laravel ha revolucionado el desarrollo PHP... [contenido completo del artículo]',
                'imagen_portada' => 'articulos/laravel-2024.jpg',
                'categoria' => 'desarrollo',
                'etiquetas' => json_encode(['laravel', 'php', 'backend', 'programacion']),
                'autor' => 'Hernan Medina',
                'fuente' => 'Funyama Blog',
                'tiempo_lectura' => 5,
                'publicado' => true,
                'destacado' => true,
                'comentarios_habilitados' => true,
                'fecha_publicacion' => now()->subDays(2),
                'autor_id_admin' => $admin->idAdmin,
            ],
            [
                'titulo' => 'Introducción a los Componentes de Livewire',
                'slug' => 'introduccion-componentes-livewire',
                'resumen' => 'Aprende a crear aplicaciones dinámicas con Laravel Livewire sin necesidad de escribir JavaScript.',
                'contenido' => 'Livewire permite crear interfaces dinámicas... [contenido completo]',
                'imagen_portada' => 'articulos/livewire-components.jpg',
                'categoria' => 'laravel',
                'etiquetas' => json_encode(['livewire', 'laravel', 'frontend', 'components']),
                'autor' => 'Hernan Medina',
                'fuente' => 'Funyama Blog',
                'tiempo_lectura' => 8,
                'publicado' => true,
                'destacado' => false,
                'comentarios_habilitados' => true,
                'fecha_publicacion' => now()->subDays(5),
                'autor_id_admin' => $admin->idAdmin,
            ]
        ];

        foreach ($articulos as $articulo) {
            Articulo::updateOrCreate(
                ['slug' => $articulo['slug']],
                $articulo
            );
        }

        $this->command->info('✅ 2 artículos de prueba creados exitosamente.');
    }
}
