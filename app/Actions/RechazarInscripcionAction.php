<?php

namespace App\Actions;

use App\Models\Solicitud;

class RechazarInscripcionAction
{
    /**
     * Rechaza una solicitud de inscripción marcándola como cancelada.
     */
    public function execute(Solicitud $solicitud, string $respuesta): void
    {
        $admin = auth()->user()?->administrador;

        $solicitud->marcarComoResuelta($respuesta, $admin?->idAdmin);
        // Usamos estado 'cancelada' para rechazos
        $solicitud->update(['estado' => 'cancelada']);
    }
}
