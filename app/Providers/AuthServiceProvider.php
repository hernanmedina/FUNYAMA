<?php

namespace App\Providers;

use App\Models\Articulo;
use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Evento;
use App\Models\Solicitud;
use App\Policies\ArticuloPolicy;
use App\Policies\CertificadoPolicy;
use App\Policies\CursoPolicy;
use App\Policies\EstudiantePolicy;
use App\Policies\EventoPolicy;
use App\Policies\SolicitudPolicy;
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
        Articulo::class => ArticuloPolicy::class,
        Certificado::class => CertificadoPolicy::class,
        Solicitud::class => SolicitudPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
