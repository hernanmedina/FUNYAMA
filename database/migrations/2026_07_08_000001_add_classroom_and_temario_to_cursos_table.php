<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cursos', function (Blueprint $table) {
            $table->string('enlace_classroom')->nullable()->after('video_presentacion');
            $table->text('temario')->nullable()->after('enlace_classroom');
        });

        Schema::table('curso_estudiante', function (Blueprint $table) {
            $table->json('temario_progreso')->nullable()->after('progreso');
        });
    }

    public function down(): void
    {
        Schema::table('curso_estudiante', function (Blueprint $table) {
            $table->dropColumn('temario_progreso');
        });

        Schema::table('cursos', function (Blueprint $table) {
            $table->dropColumn(['enlace_classroom', 'temario']);
        });
    }
};
