<?php

namespace App\Actions;

use App\Models\Estudiante;
use Illuminate\Support\Str;

class GenerarCodigoEstudianteAction
{
    /**
     * Genera un código único de estudiante con formato EST-YYYYMM-XXXX.
     */
    public function execute(): string
    {
        $year = now()->year;
        $month = now()->format('m');
        $random = strtoupper(Str::random(4));
        $codigo = "EST-{$year}{$month}-{$random}";

        while (Estudiante::where('codigo', $codigo)->exists()) {
            $random = strtoupper(Str::random(4));
            $codigo = "EST-{$year}{$month}-{$random}";
        }

        return $codigo;
    }
}
