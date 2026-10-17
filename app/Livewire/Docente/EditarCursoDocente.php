<?php

namespace App\Livewire\Docente;

use App\Models\Curso;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EditarCursoDocente extends Component
{
    use WithFileUploads;

    public Curso $curso;

    public $imagen_portada;

    public $imagen_portada_temp;

    public $nombre;

    public $descripcion;

    public $cronograma;

    public $requisitos;

    public $objetivos;

    public $materiales_incluidos;

    public $duracion_horas;

    public $duracion_texto;

    public $nivel;

    public $fecha_inicio;

    public $enlace_classroom;

    public $temario;

    public $comunidad_habilitada;

    public $comunidad_tras_finalizar;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string|min:50',
        'cronograma' => 'required|string|min:20',
        'requisitos' => 'nullable|string|min:10',
        'objetivos' => 'nullable|string|min:20',
        'materiales_incluidos' => 'nullable|string|min:20',
        'duracion_horas' => 'nullable|integer|min:1',
        'duracion_texto' => 'nullable|string|max:100',
        'nivel' => 'required|in:principiante,intermedio,avanzado',
        'imagen_portada' => 'nullable|image|max:2048',
        'fecha_inicio' => 'nullable|date',
        'enlace_classroom' => 'nullable|url',
        'temario' => 'nullable|string',
        'comunidad_habilitada' => 'boolean',
        'comunidad_tras_finalizar' => 'boolean',
    ];

    public function mount(Curso $curso): void
    {
        $this->authorize('update', $curso);

        $this->curso = $curso;
        $this->fill($curso->only([
            'nombre', 'descripcion', 'cronograma', 'requisitos', 'objetivos',
            'materiales_incluidos', 'duracion_horas', 'duracion_texto', 'nivel',
            'fecha_inicio', 'enlace_classroom', 'temario',
            'comunidad_habilitada', 'comunidad_tras_finalizar',
        ]));

        $this->imagen_portada_temp = $curso->imagen_portada;
        $this->fecha_inicio = $curso->fecha_inicio?->format('Y-m-d');
        $this->comunidad_habilitada = (bool) $curso->comunidad_habilitada;
        $this->comunidad_tras_finalizar = (bool) $curso->comunidad_tras_finalizar;
    }

    public function actualizarCurso()
    {
        $this->authorize('update', $this->curso);

        $this->validate();

        $imagenPath = $this->imagen_portada_temp;

        if ($this->imagen_portada instanceof TemporaryUploadedFile) {
            $imagenPath = $this->imagen_portada->store('cursos', 'public');

            if ($this->imagen_portada_temp && Storage::disk('public')->exists($this->imagen_portada_temp)) {
                Storage::disk('public')->delete($this->imagen_portada_temp);
            }
        }

        $this->curso->update([
            'nombre' => $this->nombre,
            'slug' => Str::slug($this->nombre),
            'descripcion' => $this->descripcion,
            'cronograma' => $this->cronograma,
            'requisitos' => $this->requisitos ?? '',
            'objetivos' => $this->objetivos,
            'materiales_incluidos' => $this->materiales_incluidos,
            'duracion_horas' => $this->duracion_horas,
            'duracion_texto' => $this->duracion_texto,
            'nivel' => $this->nivel,
            'imagen_portada' => $imagenPath,
            'fecha_inicio' => $this->fecha_inicio,
            'enlace_classroom' => $this->enlace_classroom,
            'temario' => $this->temario,
            'comunidad_habilitada' => $this->comunidad_habilitada,
            'comunidad_tras_finalizar' => $this->comunidad_tras_finalizar,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Curso actualizado correctamente.');

        return $this->redirectRoute('docente.cursos.index');
    }

    public function render()
    {
        return view('livewire.docente.editar-curso-docente', [
            'inscritos' => $this->curso->estudiantes()->count(),
        ]);
    }
}
