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
        Schema::create('inscripcion_eventos', function (Blueprint $table) {
            $table->id('idInscripcion');
            $table->unsignedBigInteger('idEvento');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('nombre');
            $table->string('apellido')->nullable();
            $table->string('email');
            $table->string('telefono')->nullable();
            $table->string('documento')->nullable();
            $table->string('estado')->default('confirmada');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('idEvento')->references('idEvento')->on('eventos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['idEvento', 'estado']);
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscripcion_eventos');
    }
};
