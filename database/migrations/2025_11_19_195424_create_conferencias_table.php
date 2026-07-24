<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conferencias', function (Blueprint $table) {
            $table->id('idConferencia');
            $table->string('titulo');
            $table->string('slug')->unique();
            $table->text('descripcion');
            $table->text('temario')->nullable();
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('ponente');
            $table->text('bio_ponente')->nullable();
            $table->string('foto_ponente')->nullable();
            $table->string('modalidad')->default('virtual');
            $table->string('enlace')->nullable();
            $table->string('lugar')->nullable();
            $table->integer('cupo_maximo')->nullable();
            $table->integer('inscritos_actual')->default(0);
            $table->decimal('costo', 8, 2)->default(0);
            $table->string('nivel')->default('general');
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
        Schema::dropIfExists('conferencias');
    }
};
