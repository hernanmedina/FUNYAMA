<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estudiantes', function (Blueprint $table) {
            $table->string('codigo')->primary(); // Código como PK (string)
            $table->unsignedBigInteger('user_id');
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['masculino', 'femenino', 'otro'])->nullable();
            $table->string('nivel_educativo')->nullable();
            $table->text('intereses')->nullable();
            $table->date('fecha_registro')->useCurrent();
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estudiantes');
    }
};
