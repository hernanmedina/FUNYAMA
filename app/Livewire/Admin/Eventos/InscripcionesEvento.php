<?php

namespace App\Livewire\Admin\Eventos;

use App\Exports\InscripcionesEventoExport;
use App\Models\Evento;
use App\Models\InscripcionEvento;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InscripcionesEvento extends Component
{
    use WithPagination;

    public Evento $evento;

    public string $search = '';

    public string $filtroEstado = '';

    public function mount(Evento $evento)
    {
        $this->evento = $evento;
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado()
    {
        $this->resetPage();
    }

    /**
     * Cancela una inscripción (libera el cupo).
     */
    public function cancelarInscripcion($idInscripcion)
    {
        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('idEvento', $this->evento->idEvento)
            ->firstOrFail();

        $this->authorize('update', $inscripcion);

        if ($inscripcion->estado === 'cancelada') {
            session()->flash('message', 'Esta inscripción ya estaba cancelada.');

            return;
        }

        $inscripcion->update(['estado' => 'cancelada']);

        // Liberar el cupo
        $this->evento->decrement('inscritos_actual');

        session()->flash('message', 'Inscripción cancelada correctamente.');
    }

    /**
     * Reactiva una inscripción cancelada (ocupa el cupo si hay disponibilidad).
     */
    public function reactivarInscripcion($idInscripcion)
    {
        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('idEvento', $this->evento->idEvento)
            ->firstOrFail();

        $this->authorize('update', $inscripcion);

        if ($inscripcion->estado === 'confirmada') {
            session()->flash('message', 'Esta inscripción ya estaba confirmada.');

            return;
        }

        // Verificar disponibilidad de cupo
        if ($this->evento->cupo_maximo !== null && $this->evento->inscritos_actual >= $this->evento->cupo_maximo) {
            session()->flash('error', 'No hay cupos disponibles para reactivar esta inscripción.');

            return;
        }

        $inscripcion->update(['estado' => 'confirmada']);

        // Ocupar el cupo
        $this->evento->increment('inscritos_actual');

        session()->flash('message', 'Inscripción reactivada correctamente.');
    }

    /**
     * Marca o desmarca el pago de una inscripción (checkbox).
     */
    public function togglePago($idInscripcion)
    {
        $inscripcion = InscripcionEvento::where('idInscripcion', $idInscripcion)
            ->where('idEvento', $this->evento->idEvento)
            ->firstOrFail();

        $this->authorize('update', $inscripcion);

        $inscripcion->update([
            'pago_realizado' => ! $inscripcion->pago_realizado,
        ]);

        $estado = $inscripcion->pago_realizado ? 'Pagado' : 'Pendiente';

        $this->dispatch('show-toast', type: 'success', message: "Inscripción marcada como: {$estado}.");
    }

    public function render()
    {
        $inscripciones = $this->consultaInscripciones()->orderByDesc('created_at')->paginate(15);

        // Resumen de pagos (solo inscripciones confirmadas)
        $pagados = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->where('pago_realizado', true)
            ->count();

        $pendientes = InscripcionEvento::where('idEvento', $this->evento->idEvento)
            ->where('estado', 'confirmada')
            ->where('pago_realizado', false)
            ->count();

        // Total recaudado por los pagos confirmados
        $totalRecaudado = $pagados * (float) $this->evento->costo;

        return view('livewire.admin.eventos.inscripciones-evento', [
            'inscripciones' => $inscripciones,
            'pagados' => $pagados,
            'pendientes' => $pendientes,
            'totalRecaudado' => $totalRecaudado,
        ]);
    }

    /**
     * Consulta base de inscripciones del evento con los filtros aplicados.
     */
    private function consultaInscripciones()
    {
        $query = InscripcionEvento::with('usuario')
            ->where('idEvento', $this->evento->idEvento);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('apellido', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
                    ->orWhere('documento', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->filtroEstado) {
            $query->where('estado', $this->filtroEstado);
        }

        return $query;
    }

    // ─── Exportación ────────────────────────────────────────────────────

    public function exportarExcel()
    {
        $this->authorize('viewAny', InscripcionEvento::class);

        $datos = $this->obtenerDatosExportables();

        if ($datos['rows'] === []) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay inscripciones para exportar con los filtros seleccionados.');

            return null;
        }

        return Excel::download(
            new InscripcionesEventoExport($datos['rows'], $datos['headings']),
            sprintf('inscripciones-%s-%s.xlsx', $this->evento->idEvento, now()->format('Y-m-d'))
        );
    }

    public function exportarCsv(): ?StreamedResponse
    {
        $this->authorize('viewAny', InscripcionEvento::class);

        $datos = $this->obtenerDatosExportables();

        if ($datos['rows'] === []) {
            $this->dispatch('show-toast', type: 'warning', message: 'No hay inscripciones para exportar con los filtros seleccionados.');

            return null;
        }

        $nombreArchivo = sprintf('inscripciones-%s-%s.csv', $this->evento->idEvento, now()->format('Y-m-d'));
        $cabeceras = array_values($datos['headings']);

        return response()->streamDownload(function () use ($datos, $cabeceras) {
            $salida = fopen('php://output', 'w');

            // BOM para compatibilidad UTF-8 con Excel
            fwrite($salida, "\xEF\xBB\xBF");

            fputcsv($salida, $cabeceras);

            foreach ($datos['rows'] as $row) {
                fputcsv($salida, array_values($row));
            }

            fclose($salida);
        }, $nombreArchivo, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Datos de las inscripciones (con filtros aplicados) listos para exportar.
     */
    private function obtenerDatosExportables(): array
    {
        $registros = $this->consultaInscripciones()
            ->orderByDesc('created_at')
            ->get();

        $esGratuito = $this->evento->es_gratuito;

        $rows = $registros->map(function (InscripcionEvento $inscripcion) use ($esGratuito) {
            $fila = [
                'nombre_completo' => trim($inscripcion->nombre.' '.$inscripcion->apellido),
                'documento' => $inscripcion->documento ?? '',
                'email' => $inscripcion->email ?? '',
                'telefono' => $inscripcion->telefono ?? '',
                'tipo_usuario' => $inscripcion->usuario
                    ? ($inscripcion->usuario->isEstudiante() ? 'Estudiante' : 'Usuario')
                    : 'Invitado',
                'estado' => $inscripcion->estado === 'confirmada' ? 'Confirmada' : 'Cancelada',
            ];

            if (! $esGratuito) {
                $fila['pago'] = $inscripcion->estado === 'confirmada'
                    ? ($inscripcion->pago_realizado ? 'Pagado' : 'Pendiente')
                    : '—';
            }

            $fila['fecha_inscripcion'] = $inscripcion->created_at
                ? Carbon::parse($inscripcion->created_at)->format('d/m/Y H:i')
                : '';

            return $fila;
        })->toArray();

        $headings = [
            'nombre_completo' => 'Nombre completo',
            'documento' => 'Documento',
            'email' => 'Email',
            'telefono' => 'Teléfono',
            'tipo_usuario' => 'Tipo de usuario',
            'estado' => 'Estado',
        ];

        if (! $esGratuito) {
            $headings['pago'] = 'Pago';
        }

        $headings['fecha_inscripcion'] = 'Fecha de inscripción';

        return ['rows' => $rows, 'headings' => $headings];
    }
}
