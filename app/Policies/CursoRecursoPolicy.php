<?php

namespace App\Policies;

use App\Models\CursoRecurso;
use App\Models\User;

class CursoRecursoPolicy
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
    public function view(User $user, CursoRecurso $recurso): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isInstructor()) {
            return $user->cursosImpartidos()->where('codigo', $recurso->curso_id)->exists();
        }

        return $this->estaInscritoEnCurso($user, $recurso->curso_id);
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
    public function update(User $user, CursoRecurso $recurso): bool
    {
        return $this->puedeGestionarCurso($user, $recurso->curso_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CursoRecurso $recurso): bool
    {
        return $this->puedeGestionarCurso($user, $recurso->curso_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CursoRecurso $recurso): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CursoRecurso $recurso): bool
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
