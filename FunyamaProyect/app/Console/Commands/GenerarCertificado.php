<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Certificado;
use App\Models\Estudiante;
use App\Models\Curso;

class GenerarCertificado extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generar-certificado {estudiante_id} {curso_id} {calificacion?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generar certificado para un estudiante que ha completado un curso';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $estudiante_id = $this->argument('estudiante_id');
        $curso_id = $this->argument('curso_id');
        $calificacion = $this->argument('calificacion');

        $estudiante = Estudiante::find($estudiante_id);
        $curso = Curso::find($curso_id);

        if (!$estudiante) {
            $this->error("Estudiante con ID {$estudiante_id} no encontrado.");
            return 1;
        }

        if (!$curso) {
            $this->error("Curso con ID {$curso_id} no encontrado.");
            return 1;
        }

        // Verificar si ya existe certificado
        $existente = Certificado::where('estudiante_id', $estudiante_id)
            ->where('curso_id', $curso_id)
            ->first();

        if ($existente) {
            $this->warn("Ya existe un certificado para este estudiante en este curso.");
            return 1;
        }

        // Crear certificado
        $certificado = Certificado::create([
            'estudiante_id' => $estudiante_id,
            'curso_id' => $curso_id,
            'numero_certificado' => Certificado::generarNumeroCertificado($estudiante_id, $curso_id),
            'fecha_emision' => now()->toDateString(),
            'calificacion_final' => $calificacion,
        ]);

        $this->info("Certificado generado correctamente!");
        $this->line("Número: {$certificado->numero_certificado}");
        $this->line("Estudiante: {$estudiante->user->name} {$estudiante->user->apellido}");
        $this->line("Curso: {$curso->nombre}");

        return 0;
    }
}
