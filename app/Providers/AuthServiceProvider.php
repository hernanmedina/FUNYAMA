<?php

namespace App\Providers;

use App\Models\Articulo;
use App\Models\Asistencia;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\CursoNetworking;
use App\Models\CursoPregunta;
use App\Models\CursoRecurso;
use App\Models\CursoRespuesta;
use App\Models\Estudiante;
use App\Models\Evento;
use App\Models\InscripcionEvento;
use App\Models\Solicitud;
use App\Models\User;
use App\Policies\ArticuloPolicy;
use App\Policies\AsistenciaPolicy;
use App\Policies\CertificadoPolicy;
use App\Policies\CursoNetworkingPolicy;
use App\Policies\CursoPolicy;
use App\Policies\CursoPreguntaPolicy;
use App\Policies\CursoRecursoPolicy;
use App\Policies\CursoRespuestaPolicy;
use App\Policies\EstudiantePolicy;
use App\Policies\EventoPolicy;
use App\Policies\InscripcionEventoPolicy;
use App\Policies\SolicitudPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Curso::class => CursoPolicy::class,
        Estudiante::class => EstudiantePolicy::class,
        Evento::class => EventoPolicy::class,
        InscripcionEvento::class => InscripcionEventoPolicy::class,
        Articulo::class => ArticuloPolicy::class,
        Certificado::class => CertificadoPolicy::class,
        Solicitud::class => SolicitudPolicy::class,
        CursoPregunta::class => CursoPreguntaPolicy::class,
        CursoRespuesta::class => CursoRespuestaPolicy::class,
        CursoRecurso::class => CursoRecursoPolicy::class,
        CursoNetworking::class => CursoNetworkingPolicy::class,
        User::class => UserPolicy::class,
        Asistencia::class => AsistenciaPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
