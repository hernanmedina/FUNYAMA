<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->string('curso_id');
            $table->string('estudiante_id');
            $table->date('fecha');
            $table->enum('estado', ['presente', 'ausente', 'tarde', 'justificado'])->default('presente');
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('registrado_por')->nullable();
            $table->timestamps();

            $table->foreign('curso_id')->references('codigo')->on('cursos')->onDelete('cascade');
            $table->foreign('estudiante_id')->references('codigo')->on('estudiantes')->onDelete('cascade');
            $table->foreign('registrado_por')->references('id')->on('users')->nullOnDelete();

            $table->unique(['curso_id', 'estudiante_id', 'fecha']);
            $table->index(['curso_id', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
