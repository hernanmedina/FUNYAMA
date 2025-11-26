<?php

use App\Livewire\Admin\Cursos\CrearCurso;
use App\Livewire\Admin\Cursos\EditarCurso;
use App\Livewire\Admin\Cursos\MostrarCurso;
use App\Livewire\Admin\Cursos\IndexCursos;
use App\Livewire\Admin\Cursos\CursosEliminados;
use App\Livewire\Admin\DashboardAdmin;
use App\Livewire\Cursos;
use App\Livewire\Estudiante\CrearEstudiante;
use App\Livewire\Estudiante\DashboardEstudiante;
use App\Livewire\Estudiante\EditarEstudiante;
use App\Livewire\Estudiante\Estudiantes;
use App\Livewire\Estudiante\EstudiantesEliminados;
use App\Livewire\Estudiante\MostrarEstudiante;
use App\Livewire\Estudiante\MisCertificados;
use Illuminate\Support\Facades\Route;

// Página principal
Route::get('/', function () {
    return view('welcome');
});

// Cursos listado público con Livewire
Route::get('/cursos', Cursos::class)->name('cursos.index');

// Rutas protegidas
Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {

    // Dashboard - REDIRECCIONES POR ROL
    Route::get('/dashboard', function () {
        $user = auth()->user();

        if ($user->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->role === 'estudiante' || $user->role === 'estu') {
            return redirect()->route('admin.estudiantes.dashboard');
        }

        return redirect()->route('cursos.index');
    })->name('dashboard');

    // Página no autorizado
    Route::get('/not-authorized', function () {
        return view('not-authorized');
    })->name('not-authorized');

    // ----------- ADMIN DASHBOARD -----------
    Route::prefix('admin')->name('admin.')->group(function () {
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
            Route::get('/dashboard', DashboardEstudiante::class)->name('dashboard');
            Route::get('/mis-certificados', MisCertificados::class)->name('certificados');
            Route::get('/{estudiante}/editar', EditarEstudiante::class)->name('edit');
            Route::get('/{estudiante}', MostrarEstudiante::class)->name('show');
            Route::get('/', Estudiantes::class)->name('index');
        });
    });
});


