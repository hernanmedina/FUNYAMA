<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use App\Models\Curso;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class EditarCurso extends Component
{
    use WithFileUploads;

    public Curso $curso;
    public $imagen_portada;
    public $imagen_portada_temp;

    // Campos del formulario
    public $nombre;
    public $descripcion;
    public $cronograma;
    public $requisitos;
    public $objetivos;
    public $materiales_incluidos;
    public $cupo_total;
    public $duracion_horas;
    public $duracion_texto;
    public $precio_regular;
    public $precio_descuento;
    public $nivel;
    public $fecha_inicio;
    public $publicado;
    public $destacado;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'descripcion' => 'required|string|min:50',
        'cronograma' => 'required|string|min:20',
        'requisitos' => 'required|string|min:20',
        'objetivos' => 'nullable|string|min:20',
        'materiales_incluidos' => 'nullable|string|min:20',
        'cupo_total' => 'required|integer|min:1',
        'duracion_horas' => 'nullable|integer|min:1',
        'duracion_texto' => 'nullable|string|max:100',
        'precio_regular' => 'required|numeric|min:0',
        'precio_descuento' => 'nullable|numeric|min:0|lt:precio_regular',
        'nivel' => 'required|in:principiante,intermedio,avanzado',
        'imagen_portada' => 'nullable|image|max:2048',
        'fecha_inicio' => 'nullable|date',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
    ];

    protected $messages = [
        'precio_descuento.lt' => 'El precio de descuento debe ser menor al precio regular.',
    ];

    public function mount(Curso $curso)
    {
        $this->curso = $curso;
        $this->fill($curso->only([
            'nombre', 'descripcion', 'cronograma', 'requisitos', 'objetivos',
            'materiales_incluidos', 'cupo_total', 'duracion_horas', 'duracion_texto',
            'precio_regular', 'precio_descuento', 'nivel', 'fecha_inicio',
            'publicado', 'destacado'
        ]));

        $this->imagen_portada_temp = $curso->imagen_portada;
    }

    public function updatedNombre($value)
    {
        $this->curso->slug = Str::slug($value);
    }

    public function actualizarCurso()
    {
        $this->validate();

        // Calcular nuevo cupo disponible
        $inscritos = $this->curso->estudiantes()->count();
        $nuevo_cupo_disponible = max(0, $this->cupo_total - $inscritos);

        // Procesar imagen
        $imagenPath = $this->imagen_portada_temp;
        if ($this->imagen_portada) {
            $imagenPath = $this->imagen_portada->store('cursos', 'public');
            // Eliminar imagen anterior si existe
            if ($this->imagen_portada_temp) {
                \Storage::disk('public')->delete($this->imagen_portada_temp);
            }
        }

        try {
            $this->curso->update([
                'nombre' => $this->nombre,
                'slug' => Str::slug($this->nombre),
                'descripcion' => $this->descripcion,
                'cronograma' => $this->cronograma,
                'requisitos' => $this->requisitos,
                'objetivos' => $this->objetivos,
                'materiales_incluidos' => $this->materiales_incluidos,
                'cupo_total' => $this->cupo_total,
                'cupo_disponible' => $nuevo_cupo_disponible,
                'duracion_horas' => $this->duracion_horas,
                'duracion_texto' => $this->duracion_texto,
                'precio_regular' => $this->precio_regular,
                'precio_descuento' => $this->precio_descuento,
                'nivel' => $this->nivel,
                'imagen_portada' => $imagenPath,
                'fecha_inicio' => $this->fecha_inicio,
                'publicado' => $this->publicado,
                'destacado' => $this->destacado,
            ]);

            $this->dispatch('show-toast',
                type: 'success',
                message: 'Curso actualizado exitosamente.'
            );

        } catch (\Exception $e) {
            $this->dispatch('show-toast',
                type: 'error',
                message: 'Error al actualizar el curso: ' . $e->getMessage()
            );
        }
    }

    public function render()
    {
        return view('livewire.admin.cursos.editar-curso', [
            'inscritos' => $this->curso->estudiantes()->count(),
        ])->layout('layouts.app');
    }
}
