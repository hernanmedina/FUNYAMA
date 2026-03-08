<?php

namespace App\Http\Middleware;

use Closure;

class RoleRedirect
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();

        if ($user) {
            switch ($user->role) {
                case 'admin':
                    return redirect('/dashboard');
                case 'estu':
                    return redirect('/estudiante/dashboard-estudiante');
            }
        }

        return redirect('/login');
    }
}



namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;


class RoleMiddleware
{
    public function handle($request, Closure $next, $role)     {
        if (!Auth::check() || Auth::user()->role !== $role) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
        return $next($request);
    }

}
