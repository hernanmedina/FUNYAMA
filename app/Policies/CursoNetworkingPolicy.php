<?php

namespace App\Policies;

use App\Models\CursoNetworking;
use App\Models\User;

class CursoNetworkingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isEstudiante();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CursoNetworking $publicacion): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->estaInscritoEnCurso($user, $publicacion->curso_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isEstudiante();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CursoNetworking $publicacion): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEstudiante() && $user->estudiante?->codigo === $publicacion->estudiante_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CursoNetworking $publicacion): bool
    {
        return $this->update($user, $publicacion);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CursoNetworking $publicacion): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CursoNetworking $publicacion): bool
    {
        return $user->isAdmin();
    }

    /**
     * Verifica que el estudiante esté inscrito en el curso indicado.
     */
    private function estaInscritoEnCurso(User $user, string $cursoId): bool
    {
        if (! $user->isEstudiante() || ! $user->estudiante) {
            return false;
        }

        return $user->estudiante->cursos()
            ->where('curso_id', $cursoId)
            ->exists();
    }
}
