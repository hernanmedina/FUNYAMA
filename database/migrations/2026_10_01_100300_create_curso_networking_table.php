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
        Schema::create('curso_networking', function (Blueprint $table) {
            $table->id();
            $table->string('curso_id');
            $table->string('estudiante_id');
            $table->text('mensaje');
            $table->enum('tipo', ['mensaje', 'presentacion', 'colaboracion'])->default('mensaje');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('curso_id')->references('codigo')->on('cursos')->cascadeOnDelete();
            $table->foreign('estudiante_id')->references('codigo')->on('estudiantes')->cascadeOnDelete();

            $table->index(['curso_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_networking');
    }
};
