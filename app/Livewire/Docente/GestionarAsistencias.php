<?php

namespace App\Livewire\Docente;

use App\Exports\AsistenciasCursoExport;
use App\Models\Asistencia;
use App\Models\Curso;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.app')]
class GestionarAsistencias extends Component
{
    public Curso $curso;

    public string $fecha = '';

    /** @var array<string, string> */
    public array $estados = [];

    /** @var array<string, string> */
    public array $observaciones = [];

    public function mount(Curso $curso): void
    {
        $this->authorize('view', $curso);

        $this->curso = $curso;
        $this->fecha = now()->format('Y-m-d');
        $this->cargarAsistencias();
    }

    public function updatedFecha(): void
    {
        $this->cargarAsistencias();
    }

    /**
     * Carga los estados existentes para la fecha seleccionada.
     */
    private function cargarAsistencias(): void
    {
        $this->estados = [];
        $this->observaciones = [];

        $registros = Asistencia::query()
            ->where('curso_id', $this->curso->codigo)
            ->whereDate('fecha', $this->fecha)
            ->get()
            ->keyBy('estudiante_id');

        foreach ($this->curso->estudiantes as $estudiante) {
            $registro = $registros->get($estudiante->codigo);

            $this->estados[$estudiante->codigo] = $registro?->estado ?? 'presente';
            $this->observaciones[$estudiante->codigo] = $registro?->observacion ?? '';
        }
    }

    /**
     * Cuenta los estados actualmente seleccionados en pantalla.
     *
     * @return array<string, int>
     */
    private function calcularResumen(): array
    {
        $resumen = [
            'presente' => 0,
            'ausente' => 0,
            'tarde' => 0,
            'justificado' => 0,
        ];

        foreach ($this->estados as $estado) {
            if (isset($resumen[$estado])) {
                $resumen[$estado]++;
            }
        }

        return $resumen;
    }

    /**
     * Exporta el resumen de asistencia del curso a Excel.
     */
    public function exportarExcel()
    {
        $this->authorize('view', $this->curso);

        $datos = $this->obtenerDatosExportables();

        if ($datos['rows'] === []) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay asistencias registradas para exportar.');

            return null;
        }

        return Excel::download(
            new AsistenciasCursoExport($datos['rows'], $datos['headings'], 'Asistencias'),
            sprintf('asistencias-%s-%s.xlsx', $this->curso->codigo, now()->format('Y-m-d'))
        );
    }

    /**
     * Resumen de asistencia por estudiante listo para exportar.
     *
     * @return array{rows: array<int, array<string, mixed>>, headings: array<int, string>}
     */
    private function obtenerDatosExportables(): array
    {
        $estudiantes = $this->curso->estudiantes()->with('user')->get()->sortBy(fn ($e) => $e->user?->name);

        $asistencias = Asistencia::query()
            ->where('curso_id', $this->curso->codigo)
            ->get()
            ->groupBy('estudiante_id');

        $rows = $estudiantes->map(function ($estudiante) use ($asistencias) {
            $registros = $asistencias->get($estudiante->codigo, collect());

            $presente = $registros->where('estado', 'presente')->count();
            $ausente = $registros->where('estado', 'ausente')->count();
            $tarde = $registros->where('estado', 'tarde')->count();
            $justificado = $registros->where('estado', 'justificado')->count();
            $total = $registros->count();

            return [
                'codigo' => $estudiante->codigo,
                'nombre_completo' => trim(($estudiante->user?->name ?? '').' '.($estudiante->user?->apellido ?? '')),
                'presente' => $presente,
                'ausente' => $ausente,
                'tarde' => $tarde,
                'justificado' => $justificado,
                'total' => $total,
                'porcentaje_asistencia' => $total > 0
                    ? round((($presente + $tarde) / $total) * 100, 1).'%'
                    : '—',
            ];
        })->values()->toArray();

        $headings = [
            'Código',
            'Estudiante',
            'Presentes',
            'Ausentes',
            'Tarde',
            'Justificados',
            'Total registros',
            '% Asistencia',
        ];

        return ['rows' => $rows, 'headings' => $headings];
    }

    public function marcarTodos(string $estado): void
    {
        $this->authorize('create', Asistencia::class);

        foreach (array_keys($this->estados) as $codigo) {
            $this->estados[$codigo] = $estado;
        }
    }

    public function guardar(): void
    {
        $this->authorize('create', Asistencia::class);

        $this->validate([
            'fecha' => ['required', 'date'],
            'estados' => ['required', 'array'],
            'estados.*' => ['required', 'in:presente,ausente,tarde,justificado'],
            'observaciones.*' => ['nullable', 'string', 'max:500'],
        ]);

        foreach ($this->estados as $estudianteId => $estado) {
            Asistencia::updateOrCreate(
                [
                    'curso_id' => $this->curso->codigo,
                    'estudiante_id' => $estudianteId,
                    'fecha' => $this->fecha,
                ],
                [
                    'estado' => $estado,
                    'observacion' => $this->observaciones[$estudianteId] ?? null,
                    'registrado_por' => Auth::id(),
                ]
            );
        }

        $this->dispatch('show-toast', type: 'success', message: 'Asistencia guardada correctamente.');
    }

    public function render()
    {
        $estudiantes = $this->curso->estudiantes()->with('user')->get()->sortBy(fn ($e) => $e->user?->name);

        return view('livewire.docente.gestionar-asistencias', [
            'estudiantes' => $estudiantes,
            'resumen' => $this->calcularResumen(),
        ]);
    }
}
