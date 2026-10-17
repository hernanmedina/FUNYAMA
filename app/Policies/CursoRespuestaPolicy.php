<?php

namespace App\Policies;

use App\Models\CursoRespuesta;
use App\Models\User;

class CursoRespuestaPolicy
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
    public function view(User $user, CursoRespuesta $respuesta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->estaInscritoEnCurso($user, $respuesta->pregunta?->curso_id);
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
    public function update(User $user, CursoRespuesta $respuesta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEstudiante() && $user->estudiante?->codigo === $respuesta->estudiante_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CursoRespuesta $respuesta): bool
    {
        return $this->update($user, $respuesta);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CursoRespuesta $respuesta): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CursoRespuesta $respuesta): bool
    {
        return $user->isAdmin();
    }

    /**
     * Verifica que el estudiante esté inscrito en el curso indicado.
     */
    private function estaInscritoEnCurso(User $user, ?string $cursoId): bool
    {
        if (! $cursoId || ! $user->isEstudiante() || ! $user->estudiante) {
            return false;
        }

        return $user->estudiante->cursos()
            ->where('curso_id', $cursoId)
            ->exists();
    }
}
