<?php

namespace App\Livewire\Admin\Eventos;

use App\Models\Evento;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CrearEvento extends Component
{
    use WithFileUploads;

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
    public $cupo_maximo = '';
    public $costo = '';
    public $tipo_evento = 'presencial';
    public $enlace_virtual = '';
    public $publicado = false;
    public $destacado = false;

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'required|string|max:500',
        'contenido' => 'nullable|string',
        'fecha' => 'required|date|after:today',
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

    public function crearEvento()
    {
        $this->validate();

        $admin = auth()->user()->administrador;

        if (! $admin) {
            session()->flash('error', 'No se encontró una cuenta de administrador asociada a este usuario.');
            return;
        }

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
            'creado_por_admin' => $admin->idAdmin,
        ];

        if ($this->imagen) {
            $data['imagen'] = $this->imagen->store('eventos', 'public');
        }

        Evento::create($data);

        session()->flash('success', 'Evento creado correctamente.');
        $this->redirect(route('admin.eventos.index'));
    }

    public function render()
    {
        return view('livewire.admin.eventos.crear-evento');
    }
}
