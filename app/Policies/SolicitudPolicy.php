<?php

namespace App\Policies;

use App\Models\Solicitud;
use App\Models\User;

class SolicitudPolicy
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
    public function view(User $user, Solicitud $solicitud): bool
    {
        // Los administradores pueden ver cualquier solicitud.
        // Un usuario solo puede ver sus propias solicitudes.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $solicitud->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Solicitud $solicitud): bool
    {
        // Los administradores pueden actualizar cualquier solicitud.
        // Un usuario solo puede actualizar sus propias solicitudes pendientes.
        if ($user->isAdmin()) {
            return true;
        }

        return $user->id === $solicitud->user_id && $solicitud->estado === 'pendiente';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Solicitud $solicitud): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Solicitud $solicitud): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Solicitud $solicitud): bool
    {
        return $user->isAdmin();
    }
}
