<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class EditarEvento extends Component
{
    use WithFileUploads;

    public $evento;
    public $titulo = '';
    public $descripcion = '';
    public $contenido = '';
    public $fecha = '';
    public $hora_inicio = '';
    public $hora_fin = '';
    public $ubicacion = '';
    public $direccion = '';
    public $ciudad = '';
    public $imagen;
    public $imagen_actual = '';
    public $cupo_maximo = '';
    public $inscritos_actual = '';
    public $costo = '';
    public $tipo_evento = 'presencial';
    public $enlace_virtual = '';
    public $publicado = false;
    public $destacado = false;

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string|max:500',
        'contenido' => 'nullable|string',
        'fecha' => 'required|date',
        'hora_inicio' => 'required|date_format:H:i',
        'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        'ubicacion' => 'nullable|string|max:255',
        'direccion' => 'nullable|string|max:255',
        'ciudad' => 'nullable|string|max:100',
        'imagen' => 'nullable|image|max:2048',
        'cupo_maximo' => 'nullable|integer|min:1',
        'costo' => 'numeric|min:0',
        'tipo_evento' => 'required|in:presencial,virtual,hibrido',
        'enlace_virtual' => 'nullable|url',
    ];

    public function mount(Evento $evento)
    {
        $this->evento = $evento;
        $this->titulo = $evento->titulo;
        $this->descripcion = $evento->descripcion;
        $this->contenido = $evento->contenido;
        $this->fecha = $evento->fecha->format('Y-m-d');
        $this->hora_inicio = $evento->hora_inicio;
        $this->hora_fin = $evento->hora_fin;
        $this->ubicacion = $evento->ubicacion;
        $this->direccion = $evento->direccion;
        $this->ciudad = $evento->ciudad;
        $this->imagen_actual = $evento->imagen;
        $this->cupo_maximo = $evento->cupo_maximo;
        $this->inscritos_actual = $evento->inscritos_actual;
        $this->costo = $evento->costo;
        $this->tipo_evento = $evento->tipo_evento;
        $this->enlace_virtual = $evento->enlace_virtual;
        $this->publicado = $evento->publicado;
        $this->destacado = $evento->destacado;
    }

    public function actualizarEvento()
    {
        $this->validate();

        $data = [
            'titulo' => $this->titulo,
            'slug' => Str::slug($this->titulo),
            'descripcion' => $this->descripcion,
            'contenido' => $this->contenido,
            'fecha' => $this->fecha,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'ubicacion' => $this->ubicacion,
            'direccion' => $this->direccion,
            'ciudad' => $this->ciudad,
            'cupo_maximo' => $this->cupo_maximo,
            'costo' => empty($this->costo) ? 0 : $this->costo,
            'tipo_evento' => $this->tipo_evento,
            'enlace_virtual' => $this->enlace_virtual,
            'publicado' => $this->publicado,
            'destacado' => $this->destacado,
        ];

        if ($this->imagen) {
            if ($this->imagen_actual) {
                \Storage::disk('public')->delete($this->imagen_actual);
            }
            $data['imagen'] = $this->imagen->store('eventos', 'public');
        }

        $this->evento->update($data);

        session()->flash('success', 'Evento actualizado correctamente.');
        $this->redirect(route('admin.eventos.index'));
    }

    public function render()
    {
        return view('livewire.admin.eventos.editar-evento');
    }
}
