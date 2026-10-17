<?php

namespace App\Policies;

use App\Models\Asistencia;
use App\Models\User;

class AsistenciaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdminOrInstructor();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Asistencia $asistencia): bool
    {
        return $this->puedeGestionarCurso($user, $asistencia->curso_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdminOrInstructor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Asistencia $asistencia): bool
    {
        return $this->puedeGestionarCurso($user, $asistencia->curso_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Asistencia $asistencia): bool
    {
        return $this->puedeGestionarCurso($user, $asistencia->curso_id);
    }

    /**
     * El admin gestiona cualquier curso; el docente solo los que imparte.
     */
    private function puedeGestionarCurso(User $user, string $cursoId): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if (! $user->isInstructor()) {
            return false;
        }

        return $user->cursosImpartidos()->where('codigo', $cursoId)->exists();
    }
}
