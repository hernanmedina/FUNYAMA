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
        Schema::create('curso_respuestas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pregunta_id')->constrained('curso_preguntas')->cascadeOnDelete();
            $table->string('estudiante_id');
            $table->text('contenido');
            $table->boolean('es_destacada')->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('estudiante_id')->references('codigo')->on('estudiantes')->cascadeOnDelete();

            $table->index(['pregunta_id', 'es_destacada']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_respuestas');
    }
};
