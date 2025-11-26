<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id('idCurso');
            $table->string('nombre');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->text('cronograma');
            $table->text('requisitos');
            $table->text('objetivos')->nullable();
            $table->text('materiales_incluidos')->nullable();
            $table->integer('cupo_total');
            $table->integer('cupo_disponible');
            $table->integer('duracion_horas')->nullable();
            $table->string('duracion_texto')->nullable();
            $table->decimal('precio_regular', 8, 2)->default(0);
            $table->decimal('precio_descuento', 8, 2)->nullable();
            $table->string('nivel')->default('principiante');
            $table->string('imagen_portada')->nullable();
            $table->string('video_presentacion')->nullable();
            $table->boolean('publicado')->default(false);
            $table->boolean('destacado')->default(false);
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->unsignedBigInteger('creado_por_admin')->nullable(); // Campo que falta
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('creado_por_admin')->references('idAdmin')->on('administradores')->onDelete('cascade');
            $table->index(['publicado', 'destacado']);
            $table->index('fecha_inicio');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
