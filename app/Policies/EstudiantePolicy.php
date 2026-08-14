<?php

namespace App\Policies;

use App\Models\Estudiante;
use App\Models\User;

class EstudiantePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Estudiante $estudiante): bool
    {
        // Los administradores pueden ver cualquier estudiante.
        // Un estudiante solo puede ver su propio perfil.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEstudiante() && $user->estudiante?->codigo === $estudiante->codigo;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Estudiante $estudiante): bool
    {
        // Los administradores pueden editar cualquier estudiante.
        // Un estudiante solo puede editar su propio perfil.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEstudiante() && $user->estudiante?->codigo === $estudiante->codigo;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Estudiante $estudiante): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Estudiante $estudiante): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Estudiante $estudiante): bool
    {
        return $user->isAdmin();
    }
}
