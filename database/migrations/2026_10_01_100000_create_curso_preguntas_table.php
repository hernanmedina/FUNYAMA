<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('curso_preguntas', function (Blueprint $table) {
            $table->id();
            $table->string('curso_id');
            $table->string('estudiante_id');
            $table->string('titulo');
            $table->text('contenido');
            $table->boolean('resuelta')->default(false);
            $table->text('respuesta_destacada')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('curso_id')->references('codigo')->on('cursos')->cascadeOnDelete();
            $table->foreign('estudiante_id')->references('codigo')->on('estudiantes')->cascadeOnDelete();

            $table->index(['curso_id', 'resuelta']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_preguntas');
    }
};
