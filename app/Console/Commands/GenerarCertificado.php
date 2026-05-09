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
    protected $signature = 'app:generar-certificado {codigo_estudiante} {codigo_curso} {calificacion?}';

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
        $codigo_estudiante = $this->argument('codigo_estudiante');
        $codigo_curso = $this->argument('codigo_curso');
        $calificacion = $this->argument('calificacion');

        $estudiante = Estudiante::where('codigo', $codigo_estudiante)->first();
        $curso = Curso::where('codigo', $codigo_curso)->first();

        if (!$estudiante) {
            $this->error("Estudiante con código {$codigo_estudiante} no encontrado.");
            return 1;
        }

        if (!$curso) {
            $this->error("Curso con código {$codigo_curso} no encontrado.");
            return 1;
        }

        // Verificar si ya existe certificado
        $existente = Certificado::where('estudiante_id', $estudiante->codigo)
            ->where('curso_id', $codigo_curso)
            ->first();

        if ($existente) {
            $this->warn("Ya existe un certificado para este estudiante en este curso.");
            return 1;
        }

        // Crear certificado
        $certificado = Certificado::create([
            'estudiante_id' => $estudiante->codigo,
            'curso_id' => $codigo_curso,
            'numero_certificado' => Certificado::generarNumeroCertificado($codigo_estudiante, $codigo_curso),
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
