<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id('idPost');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('resumen');
            $table->text('contenido');
            $table->string('imagen_portada')->nullable();
            $table->string('categoria')->default('general');
            $table->json('etiquetas')->nullable();
            $table->string('autor');
            $table->string('fuente')->nullable();
            $table->integer('vistas')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('tiempo_lectura')->nullable();
            $table->boolean('publicado')->default(false);
            $table->boolean('destacado')->default(false);
            $table->boolean('comentarios_habilitados')->default(true);
            $table->timestamp('fecha_publicacion')->nullable();
            $table->unsignedBigInteger('autor_id_admin');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('autor_id_admin')->references('idAdmin')->on('administradores')->onDelete('cascade');
            $table->index(['publicado', 'fecha_publicacion']);
            $table->index('categoria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
