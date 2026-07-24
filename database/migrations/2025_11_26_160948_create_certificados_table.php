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
        Schema::create('certificados', function (Blueprint $table) {
            $table->id();
            $table->string('estudiante_id');
            $table->string('curso_id');
            $table->string('numero_certificado')->unique();
            $table->date('fecha_emision');
            $table->decimal('calificacion_final', 3, 1)->nullable();
            $table->string('archivo_path')->nullable();
            $table->integer('descargas')->default(0);
            $table->timestamp('ultima_descarga')->nullable();
            $table->timestamps();

            $table->foreign('estudiante_id')->references('codigo')->on('estudiantes')->onDelete('cascade');
            $table->foreign('curso_id')->references('codigo')->on('cursos')->onDelete('cascade');
            $table->unique(['estudiante_id', 'curso_id']);
            $table->index(['fecha_emision']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificados');
    }
};
