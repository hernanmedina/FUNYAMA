<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('curso_estudiante', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating_estudiante')->nullable()->after('comentario_calificacion');
            $table->text('opinion_estudiante')->nullable()->after('rating_estudiante');
        });
    }

    public function down(): void
    {
        Schema::table('curso_estudiante', function (Blueprint $table) {
            $table->dropColumn(['rating_estudiante', 'opinion_estudiante']);
        });
    }
};
