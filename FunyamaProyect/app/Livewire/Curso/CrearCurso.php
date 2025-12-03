<?php

namespace App\Livewire\Curso;

use Livewire\Component;
use App\Models\Curso;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class CrearCurso extends Component
{
    public $codigo;
    public $nombre;
    public $slug;
    public $descripcion;
    public $cronograma;
    public $requisitos;
    public $objetivos;
    public $materiales_incluidos;
    public $cupo_total;
    public $cupo_disponible;
    public $duracion_horas;
    public $duracion_texto;
    public $precio_regular;
    public $precio_descuento;
    public $nivel = 'principiante';
    public $imagen_portada;
    public $video_presentacion;
    public $publicado = false;
    public $destacado = false;
    public $fecha_inicio;
    public $fecha_fin;

    protected $rules = [
        'codigo' => 'required|string|max:100|unique:cursos,codigo',
        'nombre' => 'required|string|max:255',
        'slug' => 'required|string|max:255|unique:cursos,slug',
        'descripcion' => 'required|string',
        'cronograma' => 'required|string',
        'requisitos' => 'required|string',
        'objetivos' => 'nullable|string',
        'materiales_incluidos' => 'nullable|string',
        'cupo_total' => 'required|integer|min:1',
        'cupo_disponible' => 'required|integer|min:0',
        'duracion_horas' => 'nullable|integer|min:1',
        'duracion_texto' => 'nullable|string|max:100',
        'precio_regular' => 'required|numeric|min:0',
        'precio_descuento' => 'nullable|numeric|min:0',
        'nivel' => 'required|string',
        'imagen_portada' => 'nullable|string',
        'video_presentacion' => 'nullable|string',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'fecha_inicio' => 'nullable|date',
        'fecha_fin' => 'nullable|date',
    ];

    public function store()
    {
        $this->validate();

        try {
            Curso::create([
                'codigo' => $this->codigo,
                'nombre' => $this->nombre,
                'slug' => $this->slug,
                'descripcion' => $this->descripcion,
                'cronograma' => $this->cronograma,
                'requisitos' => $this->requisitos,
                'objetivos' => $this->objetivos,
                'materiales_incluidos' => $this->materiales_incluidos,
                'cupo_total' => $this->cupo_total,
                'cupo_disponible' => $this->cupo_disponible,
                'duracion_horas' => $this->duracion_horas,
                'duracion_texto' => $this->duracion_texto,
                'precio_regular' => $this->precio_regular,
                'precio_descuento' => $this->precio_descuento,
                'nivel' => $this->nivel,
                'imagen_portada' => $this->imagen_portada,
                'video_presentacion' => $this->video_presentacion,
                'publicado' => $this->publicado,
                'destacado' => $this->destacado,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
            ]);

            session()->flash('message', 'Curso creado correctamente.');
            return redirect()->route('admin.cursos.index');
        } catch (\Exception $e) {
            $this->addError('general', 'Error al crear curso: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.curso.crear-curso');
    }
}
