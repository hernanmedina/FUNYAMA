<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$component = new App\Livewire\Cursos();
echo "mostrarModalSolicitud inicial: ".($component->mostrarModalSolicitud ? 'true' : 'false')."\n";

$curso = App\Models\Curso::where('publicado', true)->first();
if ($curso) {
    echo "Curso: {$curso->codigo} - {$curso->nombre}\n";
    $component->abrirModalSolicitud($curso->codigo);
    echo "mostrarModalSolicitud despues: ".($component->mostrarModalSolicitud ? 'true' : 'false')."\n";
    echo "cursoSeleccionado: ".($component->cursoSeleccionado?->nombre ?? 'null')."\n";
} else {
    echo "NO HAY CURSOS PUBLICADOS\n";
    $count = App\Models\Curso::count();
    echo "Total cursos en BD: $count\n";
}
