<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_soft_deletes_to_estudiantes_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('estudiantes', function (Blueprint $table) {
            $table->softDeletes(); // Esto agrega la columna deleted_at
        });
    }

    public function down()
    {
        Schema::table('estudiante', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
