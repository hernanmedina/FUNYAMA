<?php

use App\Livewire\Admin\Blog\CrearBlog;
use App\Livewire\Admin\Blog\EditarBlog;
use App\Livewire\Admin\Blog\IndexBlog;
use App\Livewire\Admin\ControlPagos;
use App\Livewire\Admin\Cursos\CrearCurso;
use App\Livewire\Admin\Cursos\CursosEliminados;
use App\Livewire\Admin\Cursos\EditarCurso;
use App\Livewire\Admin\Cursos\IndexCursos;
use App\Livewire\Admin\Cursos\MostrarCurso;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Admin\Eventos\CrearEvento;
use App\Livewire\Admin\Eventos\EditarEvento;
use App\Livewire\Admin\Eventos\IndexEventos;
use App\Livewire\Admin\GestionarCertificados;
use App\Livewire\Admin\OpinionesEstudiantes;
use App\Livewire\Admin\Solicitudes\SolicitudesInscripcion;
use App\Livewire\Blog;
use App\Livewire\BlogDetalle;
use App\Livewire\CalendarioEventos;
use App\Livewire\Cursos;
use App\Livewire\Estudiante\CrearEstudiante;
use App\Livewire\Estudiante\DashboardEstudiante;
use App\Livewire\Estudiante\EditarEstudiante;
use App\Livewire\Estudiante\Estudiantes;
use App\Livewire\Estudiante\EstudiantesEliminados;
use App\Livewire\Estudiante\MisCertificados;
use App\Livewire\Estudiante\MisCursos;
use App\Livewire\Estudiante\MostrarEstudiante;
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
    return view('welcome');
});

// Cursos listado público con Livewire
Route::get('/cursos', Cursos::class)->name('cursos.index');
Route::get('/cursos/{curso}', MostrarCurso::class)->name('cursos.show');

// Calendario de eventos público
Route::get('/eventos', CalendarioEventos::class)->name('eventos.index');

// Blog y Noticias público
Route::get('/blog', Blog::class)->name('blog.index');
Route::get('/blog/{articulo}', BlogDetalle::class)->name('blog.detalle');

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
            Route::get('/{evento}/editar', EditarEvento::class)->name('edit');
        });
        // Gestión de solicitudes de inscripción
        Route::prefix('solicitudes')->name('solicitudes.')->group(function () {
            Route::get('/inscripcion', SolicitudesInscripcion::class)->name('inscripcion');
        });

        // Gestión de certificados
        Route::get('/certificados', GestionarCertificados::class)->name('certificados');

        // Opiniones de estudiantes
        Route::get('/opiniones', OpinionesEstudiantes::class)->name('opiniones');

        // Control de pagos
        Route::get('/pagos', ControlPagos::class)->name('pagos');

        // Blog / Noticias
        Route::prefix('blog')->name('blog.')->group(function () {
            Route::get('/', IndexBlog::class)->name('index');
            Route::get('/crear', CrearBlog::class)->name('create');
            Route::get('/{articulo}/editar', EditarBlog::class)->name('edit');
        });
    });
});
