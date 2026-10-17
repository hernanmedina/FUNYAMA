<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class DocenteMiddleware
{
    /**
     * Permite el acceso a administradores y docentes (instructores).
     */
    public function handle($request, Closure $next)
    {
        if (! Auth::check() || ! Auth::user()->isAdminOrInstructor()) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }

        return $next($request);
    }
}
