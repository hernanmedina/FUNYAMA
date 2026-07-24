<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curso_estudiante', function (Blueprint $table) {
            $table->id();
            $table->string('curso_id');
            $table->string('estudiante_id');
            $table->enum('estado', ['inscrito', 'en_progreso', 'completado', 'cancelado'])->default('inscrito');
            $table->decimal('calificacion', 3, 1)->nullable();
            $table->text('comentario_calificacion')->nullable();
            $table->decimal('pago_realizado', 8, 2)->default(0);
            $table->enum('estado_pago', ['pendiente', 'parcial', 'completo'])->default('pendiente');
            $table->timestamp('fecha_inscripcion')->useCurrent();
            $table->timestamp('fecha_completado')->nullable();
            $table->integer('progreso')->default(0);
            $table->text('notas_administrativas')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('curso_id')->references('codigo')->on('cursos')->onDelete('cascade');
            $table->foreign('estudiante_id')->references('codigo')->on('estudiantes')->onDelete('cascade');

            $table->unique(['curso_id', 'estudiante_id']);
            $table->index(['estado', 'estado_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_estudiante');
    }
};
