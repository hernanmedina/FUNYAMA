<?php

use App\Livewire\Admin\Asistencias\GestionarAsistencias as GestionarAsistenciasAdmin;
use App\Livewire\Admin\Blog\CrearBlog;
use App\Livewire\Admin\Blog\EditarBlog;
use App\Livewire\Admin\Blog\IndexBlog;
use App\Livewire\Admin\CalificarEstudiantes;
use App\Livewire\Admin\ControlPagos;
use App\Livewire\Admin\Cursos\CrearCurso;
use App\Livewire\Admin\Cursos\CursosEliminados;
use App\Livewire\Admin\Cursos\EditarCurso;
use App\Livewire\Admin\Cursos\GestionarRecursosCurso;
use App\Livewire\Admin\Cursos\IndexCursos;
use App\Livewire\Admin\Cursos\MostrarCurso;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Admin\Eventos\CrearEvento;
use App\Livewire\Admin\Eventos\EditarEvento;
use App\Livewire\Admin\Eventos\IndexEventos;
use App\Livewire\Admin\Eventos\IngresosEventos;
use App\Livewire\Admin\Eventos\InscripcionesEvento;
use App\Livewire\Admin\Eventos\InscritosEventos;
use App\Livewire\Admin\Eventos\MostrarEvento;
use App\Livewire\Admin\Eventos\PagosPendientesEventos;
use App\Livewire\Admin\GestionarCertificados;
use App\Livewire\Admin\OpinionesEstudiantes;
use App\Livewire\Admin\PersonalizarSitio;
use App\Livewire\Admin\Solicitudes\SolicitudesInscripcion;
use App\Livewire\Admin\Usuarios\CrearUsuario;
use App\Livewire\Admin\Usuarios\EditarUsuario;
use App\Livewire\Admin\Usuarios\IndexUsuarios;
use App\Livewire\Auth\EstablecerPassword;
use App\Livewire\Blog;
use App\Livewire\BlogDetalle;
use App\Livewire\CalendarioEventos;
use App\Livewire\Cursos;
use App\Livewire\Docente\DashboardDocente;
use App\Livewire\Docente\EditarCursoDocente;
use App\Livewire\Docente\GestionarAsistencias as GestionarAsistenciasDocente;
use App\Livewire\Docente\GestionarRecursosDocente;
use App\Livewire\Docente\MisCursosDocente;
use App\Livewire\Estudiante\ComunidadCurso;
use App\Livewire\Estudiante\CrearEstudiante;
use App\Livewire\Estudiante\DashboardEstudiante;
use App\Livewire\Estudiante\EditarEstudiante;
use App\Livewire\Estudiante\Estudiantes;
use App\Livewire\Estudiante\EstudiantesEliminados;
use App\Livewire\Estudiante\MisCertificados;
use App\Livewire\Estudiante\MisCursos;
use App\Livewire\Estudiante\MisEventos;
use App\Livewire\Estudiante\MostrarEstudiante;
use App\Livewire\EventoDetalle;
use App\Livewire\Opiniones;
use App\Models\Configuracion;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

// Configurar rutas de Livewire
// Personalizar la ruta de actualización de Livewire (POST)
Livewire::setUpdateRoute(function ($handle) {
    return Route::post('/livewire/update', $handle)
        ->middleware(['web', 'throttle:60,1']);
});

// Página principal
Route::get('/', function () {
    return view('welcome', [
        'configuracion' => Configuracion::mapa(),
    ]);
})->name('home');

// Cursos listado público con Livewire
Route::get('/cursos', Cursos::class)->name('cursos.index');
Route::get('/cursos/{curso}', MostrarCurso::class)->name('cursos.show');

// Calendario de eventos público
Route::get('/eventos', CalendarioEventos::class)->name('eventos.index');
Route::get('/eventos/{evento}', EventoDetalle::class)->name('eventos.show');

// Blog y Noticias público
Route::get('/blog', Blog::class)->name('blog.index');
Route::get('/blog/{articulo}', BlogDetalle::class)->name('blog.detalle');

// Opiniones de estudiantes público
Route::get('/opiniones', Opiniones::class)->name('opiniones.index');

// Establecer contraseña desde invitación (público)
Route::get('/establecer-password/{token}', EstablecerPassword::class)->name('password.establecer');

// Rutas protegidas
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard - REDIRECCIONES POR ROL
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isEstudiante()) {
            return redirect()->route('estudiante.dashboard');
        }

        if ($user->isInstructor()) {
            return redirect()->route('docente.dashboard');
        }

        return redirect()->route('cursos.index');
    })->name('dashboard');

    // Página no autorizado
    Route::get('/not-authorized', function () {
        return view('not-authorized');
    })->name('not-authorized');

    // ----------- RUTAS DE ESTUDIANTE (ALUMNO LOGUEADO) -----------
    Route::prefix('estudiante')->name('estudiante.')->middleware('role:estu')->group(function () {
        Route::get('/dashboard', DashboardEstudiante::class)->name('dashboard');
        Route::get('/mis-certificados', MisCertificados::class)->name('certificados');
        Route::get('/mis-cursos', MisCursos::class)->name('mis-cursos');
        Route::get('/mis-eventos', MisEventos::class)->name('mis-eventos');
        Route::get('/cursos/{curso}/comunidad', ComunidadCurso::class)->name('cursos.comunidad');
    });

    // ----------- PANEL DE DOCENTE -----------
    Route::prefix('docente')->name('docente.')->middleware('docente')->group(function () {
        Route::get('/dashboard', DashboardDocente::class)->name('dashboard');

        Route::prefix('cursos')->name('cursos.')->group(function () {
            Route::get('/', MisCursosDocente::class)->name('index');
            Route::get('/{curso}/editar', EditarCursoDocente::class)->name('editar');
            Route::get('/{curso}/recursos', GestionarRecursosDocente::class)->name('recursos');
            Route::get('/{curso}/asistencias', GestionarAsistenciasDocente::class)->name('asistencias');
        });
    });

    // ----------- ADMIN DASHBOARD -----------
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {
        // Dashboard administrativo
        Route::get('/dashboard', DashboardAdmin::class)->name('dashboard');

        // Gestión de cursos
        Route::prefix('cursos')->name('cursos.')->group(function () {
            Route::get('/', IndexCursos::class)->name('index');
            Route::get('/eliminados', CursosEliminados::class)->name('eliminados');
            Route::get('/crear', CrearCurso::class)->name('create');
            Route::get('/{curso}', MostrarCurso::class)->name('show');
            Route::get('/{curso}/editar', EditarCurso::class)->name('edit');
            Route::get('/{curso}/recursos', GestionarRecursosCurso::class)->name('recursos');
        });
        // Gestión de estudiantes (ruta plural: estudiantes)
        Route::prefix('estudiantes')->name('estudiantes.')->group(function () {
            Route::get('/crear', CrearEstudiante::class)->name('create');
            Route::get('/eliminados', EstudiantesEliminados::class)->name('eliminados');
            Route::get('/{estudiante}/editar', EditarEstudiante::class)->name('edit');
            Route::get('/{estudiante}', MostrarEstudiante::class)->name('show');
            Route::get('/', Estudiantes::class)->name('index');
        });
        // Gestión de eventos
        Route::prefix('eventos')->name('eventos.')->group(function () {
            Route::get('/', IndexEventos::class)->name('index');
            Route::get('/crear', CrearEvento::class)->name('create');
            Route::get('/pagos-pendientes', PagosPendientesEventos::class)->name('pagos-pendientes');
            Route::get('/inscritos', InscritosEventos::class)->name('inscritos');
            Route::get('/ingresos', IngresosEventos::class)->name('ingresos');
            Route::get('/{evento}', MostrarEvento::class)->name('show');
            Route::get('/{evento}/editar', EditarEvento::class)->name('edit');
            Route::get('/{evento}/inscripciones', InscripcionesEvento::class)->name('inscripciones');
        });
        // Gestión de solicitudes de inscripción
        Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
            Route::get('/inscripcion', SolicitudesInscripcion::class)->name('inscripcion');
        });

        // Gestión de certificados
        Route::get('/certificados', GestionarCertificados::class)->name('certificados');

        // Opiniones de estudiantes
        Route::get('/opiniones', OpinionesEstudiantes::class)->name('opiniones');

        // Personalización del sitio
        Route::get('/personalizar', PersonalizarSitio::class)->name('personalizar');

        // Control de pagos
        Route::get('/pagos', ControlPagos::class)->name('pagos');

        // Calificar estudiantes
        Route::get('/calificar', CalificarEstudiantes::class)->name('calificar');

        // Control de asistencias
        Route::get('/asistencias', GestionarAsistenciasAdmin::class)->name('asistencias');

        // Blog / Noticias
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', IndexBlog::class)->name('index');
            Route::get('/crear', CrearBlog::class)->name('create');
            Route::get('/{articulo}/editar', EditarBlog::class)->name('edit');
        });

        // Gestión de usuarios (administradores y docentes)
        Route::prefix('usuarios')->name('usuarios.')->group(function () {
            Route::get('/', IndexUsuarios::class)->name('index');
            Route::get('/crear', CrearUsuario::class)->name('create');
            Route::get('/{usuario}/editar', EditarUsuario::class)->name('edit');
        });
    });
});
