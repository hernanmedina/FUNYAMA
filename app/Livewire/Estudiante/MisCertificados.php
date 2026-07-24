<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\Certificado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MisCertificados extends Component
{
    public $certificados;
    public $filtroEstado = 'todos';

    public function mount()
    {
        $this->cargarCertificados();
    }

    public function cargarCertificados()
    {
        $user = Auth::user();
        $estudiante = $user->estudiante;

        if ($estudiante) {
            $query = $estudiante->certificados()
                ->with('curso')
                ->orderBy('fecha_emision', 'desc');

            $this->certificados = $query->get();
        } else {
            $this->certificados = collect();
        }
    }

    public function descargarCertificado($certificadoId)
    {
        $certificado = Certificado::findOrFail($certificadoId);

        // Verificar que pertenece al estudiante actual
        if ($certificado->estudiante_id !== Auth::user()->estudiante->codigo) {
            session()->flash('error', 'No tienes acceso a este certificado.');
            return;
        }

        // Verificar que el archivo exista
        if (!$certificado->archivo_path || !Storage::disk('public')->exists($certificado->archivo_path)) {
            session()->flash('error', 'El archivo del certificado no está disponible. Contacta al administrador.');
            return;
        }

        // Registrar descarga
        $certificado->registrarDescarga();

        // Retornar la descarga del archivo PDF
        return response()->download(
            Storage::disk('public')->path($certificado->archivo_path),
            'certificado_' . $certificado->curso->nombre . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.estudiante.mis-certificados', [
            'certificados' => $this->certificados,
        ])->layout('layouts.app'); ;
    }
}
