<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id('idDocente');
            $table->unsignedBigInteger('user_id');
            $table->string('especialidad')->nullable();
            $table->text('biografia')->nullable();
            $table->string('foto')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->date('fecha_ingreso')->useCurrent();
            $table->boolean('activo')->default(true);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};
