<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('eventos', function (Blueprint $table) {
            $table->id('idEvento');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->text('contenido')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('ubicacion');
            $table->string('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('imagen')->nullable();
            $table->integer('cupo_maximo')->nullable();
            $table->integer('inscritos_actual')->default(0);
            $table->decimal('costo', 8, 2)->default(0);
            $table->string('tipo_evento')->default('presencial');
            $table->string('enlace_virtual')->nullable();
            $table->boolean('publicado')->default(false);
            $table->boolean('destacado')->default(false);
            $table->unsignedBigInteger('creado_por_admin');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('creado_por_admin')->references('idAdmin')->on('administradores')->onDelete('cascade');
            $table->index(['fecha', 'publicado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('eventos');
    }
};
