<?php

namespace App\Policies;

use App\Models\CursoPregunta;
use App\Models\User;

class CursoPreguntaPolicy
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
    public function view(User $user, CursoPregunta $pregunta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $this->estaInscritoEnCurso($user, $pregunta->curso_id);
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
    public function update(User $user, CursoPregunta $pregunta): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isEstudiante() && $user->estudiante?->codigo === $pregunta->estudiante_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CursoPregunta $pregunta): bool
    {
        return $this->update($user, $pregunta);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CursoPregunta $pregunta): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CursoPregunta $pregunta): bool
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
