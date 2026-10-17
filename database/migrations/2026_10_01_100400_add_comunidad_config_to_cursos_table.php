<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->boolean('comunidad_habilitada')->default(true)->after('destacado');
            $table->boolean('comunidad_tras_finalizar')->default(false)->after('comunidad_habilitada');
        });
    }

    public function down(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn(['comunidad_habilitada', 'comunidad_tras_finalizar']);
        });
    }
};
