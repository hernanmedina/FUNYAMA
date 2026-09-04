<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inscripcion_eventos', function (Blueprint $table) {
            // Indica si el inscrito ya realizó el pago del evento.
            $table->boolean('pago_realizado')->default(false)->after('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripcion_eventos', function (Blueprint $table) {
            $table->dropColumn('pago_realizado');
        });
    }
};
