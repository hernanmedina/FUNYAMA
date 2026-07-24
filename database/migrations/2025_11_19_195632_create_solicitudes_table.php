<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes', function (Blueprint $table) {
            $table->id('idSolicitud');
            $table->enum('tipo', ['informacion', 'inscripcion', 'problema', 'sugerencia', 'otro']);
            $table->string('asunto');
            $table->text('mensaje');
            $table->string('telefono')->nullable();
            $table->string('email_contacto')->nullable();
            $table->json('datos_adicionales')->nullable();
            $table->enum('estado', ['pendiente', 'en_proceso', 'resuelta', 'cancelada'])->default('pendiente');
            $table->text('respuesta')->nullable();
            $table->timestamp('fecha_respuesta')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('atendido_por_admin')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('atendido_por_admin')->references('idAdmin')->on('administradores')->onDelete('set null');
            $table->index(['estado', 'tipo']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes');
    }
};
