<?php

namespace App\Livewire\Admin;

use App\Models\Certificado;
use App\Models\Curso;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class GestionarCertificados extends Component
{
    use WithFileUploads;

    public $estudiantes;

    public $cursos;

    public $certificados;

    public $searchEstudiante = '';

    public $searchCurso = '';

    public $cursosEstudiante;

    // Modal de carga
    public bool $mostrarModalCarga = false;

    public ?string $estudianteSeleccionado = null;

    public ?string $cursoSeleccionado = null;

    public $archivoPDF = null;

    public ?string $certificadoEditandoId = null;

    // Filtros
    public $filtroEstudiante = '';

    public $filtroCurso = '';

    protected function rules()
    {
        return [
            'estudianteSeleccionado' => 'required|exists:estudiantes,codigo',
            'cursoSeleccionado' => 'required|exists:cursos,codigo',
            'archivoPDF' => 'required|file|mimes:pdf|max:10240', // 10MB max
        ];
    }

    protected $messages = [
        'estudianteSeleccionado.required' => 'Debes seleccionar un estudiante.',
        'estudianteSeleccionado.exists' => 'El estudiante seleccionado no existe.',
        'cursoSeleccionado.required' => 'Debes seleccionar un curso.',
        'cursoSeleccionado.exists' => 'El curso seleccionado no existe.',
        'archivoPDF.required' => 'Debes seleccionar un archivo PDF.',
        'archivoPDF.file' => 'El archivo debe ser un PDF válido.',
        'archivoPDF.mimes' => 'El archivo debe ser un PDF.',
        'archivoPDF.max' => 'El archivo no debe superar los 10MB.',
    ];

    public function mount()
    {
        $this->cursosEstudiante = collect();
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $this->estudiantes = Estudiante::with('user')
            ->where('activo', true)
            ->orderBy('created_at', 'desc')
            ->get();

        $this->cursos = Curso::where('publicado', true)
            ->orderBy('nombre')
            ->get();

        $this->cargarCertificados();
    }

    public function cargarCertificados()
    {
        $query = Certificado::with(['estudiante.user', 'curso'])
            ->orderBy('created_at', 'desc');

        if ($this->filtroEstudiante) {
            $query->where('estudiante_id', $this->filtroEstudiante);
        }

        if ($this->filtroCurso) {
            $query->where('curso_id', $this->filtroCurso);
        }

        $this->certificados = $query->get();
    }

    public function updatedFiltroEstudiante()
    {
        $this->cargarCertificados();
    }

    public function updatedFiltroCurso()
    {
        $this->cargarCertificados();
    }

    public function updatedEstudianteSeleccionado($value)
    {
        $this->cargarCursosEstudiante();
        $this->cursoSeleccionado = null; // Reset course selection when student changes
    }

    public function cargarCursosEstudiante()
    {
        if ($this->estudianteSeleccionado) {
            $estudiante = Estudiante::find($this->estudianteSeleccionado);
            if ($estudiante) {
                $this->cursosEstudiante = $estudiante->cursos()
                    ->orderBy('nombre')
                    ->get();
            } else {
                $this->cursosEstudiante = collect();
            }
        } else {
            $this->cursosEstudiante = collect();
        }
    }

    public function abrirModalCarga()
    {
        $this->reset(['estudianteSeleccionado', 'cursoSeleccionado', 'archivoPDF', 'certificadoEditandoId']);
        if ($this->filtroEstudiante) {
            $this->estudianteSeleccionado = $this->filtroEstudiante;
        }
        $this->cargarCursosEstudiante();
        $this->mostrarModalCarga = true;
    }

    public function cerrarModal()
    {
        $this->mostrarModalCarga = false;
        $this->reset(['estudianteSeleccionado', 'cursoSeleccionado', 'archivoPDF', 'certificadoEditandoId']);
        $this->cursosEstudiante = collect();
        $this->resetValidation();
    }

    public function subirCertificado()
    {
        $this->authorize('create', Certificado::class);

        $this->validate();

        try {
            // Verificar si ya existe un certificado para este estudiante y curso
            $certificadoExistente = Certificado::where('estudiante_id', $this->estudianteSeleccionado)
                ->where('curso_id', $this->cursoSeleccionado)
                ->first();

            if ($certificadoExistente && ! $this->certificadoEditandoId) {
                $this->dispatch('show-toast',
                    type: 'error',
                    message: 'Este estudiante ya tiene un certificado para este curso. Puedes editarlo desde la lista.'
                );

                return;
            }

            // Guardar el archivo PDF
            $estudiante = Estudiante::with('user')->find($this->estudianteSeleccionado);
            $curso = Curso::find($this->cursoSeleccionado);

            $nombreArchivo = 'certificado_'.$estudiante->codigo.'_'.$curso->codigo.'_'.time().'.pdf';
            $ruta = $this->archivoPDF->storeAs('certificados', $nombreArchivo, 'public');

            if (! $ruta) {
                throw new \Exception('Error al guardar el archivo PDF.');
            }

            if ($this->certificadoEditandoId) {
                // Actualizar certificado existente
                $certificado = Certificado::findOrFail($this->certificadoEditandoId);

                // Eliminar archivo anterior si existe
                if ($certificado->archivo_path && Storage::disk('public')->exists($certificado->archivo_path)) {
                    Storage::disk('public')->delete($certificado->archivo_path);
                }

                $certificado->update([
                    'archivo_path' => $ruta,
                ]);

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Certificado actualizado correctamente.'
                );
            } else {
                // Crear nuevo certificado
                Certificado::create([
                    'estudiante_id' => $this->estudianteSeleccionado,
                    'curso_id' => $this->cursoSeleccionado,
                    'numero_certificado' => Certificado::generarNumeroCertificado(
                        $this->estudianteSeleccionado,
                        $this->cursoSeleccionado
                    ),
                    'fecha_emision' => now(),
                    'archivo_path' => $ruta,
                    'descargas' => 0,
                ]);

                $this->dispatch('show-toast',
                    type: 'success',
                    message: 'Certificado subido correctamente.'
                );
            }

            $this->cerrarModal();
            $this->cargarCertificados();

        } catch (\Exception $e) {
            Log::error('Error al subir certificado: '.$e->getMessage());
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Error al subir el certificado: '.$e->getMessage()
            );
        }
    }

    public function editarCertificado($certificadoId)
    {
        $certificado = Certificado::findOrFail($certificadoId);

        $this->authorize('update', $certificado);

        $this->certificadoEditandoId = $certificadoId;
        $this->estudianteSeleccionado = $certificado->estudiante_id;
        $this->cargarCursosEstudiante();
        $this->cursoSeleccionado = $certificado->curso_id;
        $this->archivoPDF = null;
        $this->mostrarModalCarga = true;
    }

    public function eliminarCertificado($certificadoId)
    {
        $certificado = Certificado::findOrFail($certificadoId);

        $this->authorize('delete', $certificado);

        // Eliminar archivo físico
        if ($certificado->archivo_path && Storage::disk('public')->exists($certificado->archivo_path)) {
            Storage::disk('public')->delete($certificado->archivo_path);
        }

        $certificado->delete();

        $this->dispatch('show-toast',
            type: 'success',
            message: 'Certificado eliminado correctamente.'
        );

        $this->cargarCertificados();
    }

    public function render()
    {
        return view('livewire.admin.gestionar-certificados')
            ->layout('layouts.app');
    }
}
