<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reemplazar el rol 'user' por 'instructor' en los registros existentes
        DB::table('users')->where('role', 'user')->update(['role' => 'instructor']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'estu', 'instructor'])->default('instructor')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los registros 'instructor' a 'user'
        DB::table('users')->where('role', 'instructor')->update(['role' => 'user']);

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'estu', 'user'])->default('user')->change();
        });
    }
};
