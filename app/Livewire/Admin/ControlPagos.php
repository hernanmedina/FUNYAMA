<?php

namespace App\Livewire\Admin;

use App\Models\Curso;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class ControlPagos extends Component
{
    use WithPagination;

    public string $search = '';

    public ?string $estadoPagoFilter = null;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function setEstadoPagoFilter(?string $estado): void
    {
        $this->estadoPagoFilter = $this->estadoPagoFilter === $estado ? null : $estado;
        $this->resetPage();
    }

    public function actualizarEstadoPago(string $cursoId, string $estudianteId, string $estadoPago): void
    {
        $estadoPago = in_array($estadoPago, ['pendiente', 'parcial', 'completo'], true)
            ? $estadoPago
            : 'pendiente';

        $curso = Curso::find($cursoId);
        if (! $curso) {
            $this->dispatch('show-toast', type: 'error', message: 'No se encontró el curso asociado a la matrícula.');

            return;
        }

        $payload = [
            'estado_pago' => $estadoPago,
        ];

        if ($estadoPago === 'pendiente') {
            $payload['pago_realizado'] = 0;
        }

        if ($estadoPago === 'completo') {
            $payload['pago_realizado'] = (float) $curso->precioFinal;
        }

        DB::table('curso_estudiante')
            ->where('curso_id', $cursoId)
            ->where('estudiante_id', $estudianteId)
            ->update($payload);

        $this->dispatch('show-toast', type: 'success', message: 'Estado de pago actualizado correctamente.');
    }

    public function marcarCursoCompletado(string $cursoId, string $estudianteId): void
    {
        $inscripcion = DB::table('curso_estudiante')
            ->where('curso_id', $cursoId)
            ->where('estudiante_id', $estudianteId)
            ->first();

        if (! $inscripcion) {
            $this->dispatch('show-toast', type: 'error', message: 'No se encontró la matrícula.');

            return;
        }

        if ($inscripcion->estado_pago !== 'completo') {
            $this->dispatch('show-toast', type: 'error', message: 'Debes marcar el pago como completo antes de finalizar el curso.');

            return;
        }

        DB::table('curso_estudiante')
            ->where('curso_id', $cursoId)
            ->where('estudiante_id', $estudianteId)
            ->update([
                'estado' => 'completado',
                'fecha_completado' => now(),
                'progreso' => 100,
            ]);

        $this->dispatch('show-toast', type: 'success', message: 'Curso marcado como finalizado correctamente.');
    }

    public function render()
    {
        $query = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->join('estudiantes as e', 'e.codigo', '=', 'ce.estudiante_id')
            ->join('users as u', 'u.id', '=', 'e.user_id')
            ->whereNull('c.deleted_at')
            ->whereNull('e.deleted_at');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('c.nombre', 'like', '%'.$this->search.'%')
                    ->orWhere('u.name', 'like', '%'.$this->search.'%')
                    ->orWhere('u.apellido', 'like', '%'.$this->search.'%');
            });
        }

        if ($this->estadoPagoFilter) {
            $query->where('ce.estado_pago', $this->estadoPagoFilter);
        }

        $matriculas = $query
            ->select([
                'ce.curso_id',
                'ce.estudiante_id',
                'c.nombre as curso_nombre',
                'u.name',
                'u.apellido',
                'ce.estado_pago',
                'ce.estado',
                'ce.pago_realizado',
                'ce.fecha_inscripcion',
            ])
            ->orderByDesc('ce.fecha_inscripcion')
            ->paginate(20);

        // Estadisticas rapidas
        $totales = DB::table('curso_estudiante as ce')
            ->join('cursos as c', 'c.codigo', '=', 'ce.curso_id')
            ->whereNull('c.deleted_at')
            ->selectRaw("
                COUNT(*) as total,
                SUM(CASE WHEN ce.estado_pago = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN ce.estado_pago = 'parcial' THEN 1 ELSE 0 END) as parciales,
                SUM(CASE WHEN ce.estado_pago = 'completo' THEN 1 ELSE 0 END) as completos,
                COALESCE(SUM(ce.pago_realizado), 0) as total_recaudado
            ")
            ->first();

        return view('livewire.admin.control-pagos', [
            'matriculas' => $matriculas,
            'totales' => $totales,
        ])->layout('layouts.app');
    }
}
