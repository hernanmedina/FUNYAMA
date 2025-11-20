<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('administradores', function (Blueprint $table) {
            $table->id('idAdmin');
            $table->unsignedBigInteger('user_id');
            $table->string('departamento')->nullable();
            $table->string('cargo')->nullable();
            $table->string('telefono_contacto')->nullable();
            $table->text('permisos')->nullable();
            $table->boolean('super_admin')->default(false);
            $table->date('fecha_ingreso')->useCurrent();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('administradores');
    }
};
