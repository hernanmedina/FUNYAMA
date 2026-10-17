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
        Schema::create('curso_recursos', function (Blueprint $table) {
            $table->id();
            $table->string('curso_id');
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->enum('tipo', ['plantilla', 'guia', 'recurso'])->default('recurso');
            $table->string('archivo_path')->nullable();
            $table->string('enlace')->nullable();
            $table->string('version')->default('1.0');
            $table->boolean('publicado')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('curso_id')->references('codigo')->on('cursos')->cascadeOnDelete();

            $table->index(['curso_id', 'tipo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curso_recursos');
    }
};
