<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\Estudiante;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] // Cambiar a layouts.app
class EditarEstudiante extends Component
{
    public $estudianteId;

    public $name;
    public $apellido;
    public $email;
    public $telefono;

    public $matricula;
    public $fecha_nacimiento;
    public $genero;
    public $nivel_educativo;
    public $intereses;
    public $activo;

    protected $rules = [
        'name' => 'required|string|max:255',
        'apellido' => 'nullable|string|max:255',
        'email' => 'required|email',
        'telefono' => 'nullable|string|max:30',

        'matricula' => 'nullable|string|max:100',
        'fecha_nacimiento' => 'nullable|date',
        'genero' => 'nullable|in:masculino,femenino,otro',
        'nivel_educativo' => 'nullable|string|max:255',
        'intereses' => 'nullable|string',
        'activo' => 'boolean',
    ];

    public function mount($estudiante)
    {
        $e = Estudiante::with('user')->findOrFail($estudiante);
        $this->estudianteId = $e->idEstudiante;

        $this->name = $e->user->name;
        $this->apellido = $e->user->apellido;
        $this->email = $e->user->email;
        $this->telefono = $e->user->telefono;

        $this->matricula = $e->matricula;
        $this->fecha_nacimiento = $e->fecha_nacimiento ? (string) $e->fecha_nacimiento : null;
        $this->genero = $e->genero;
        $this->nivel_educativo = $e->nivel_educativo;
        $this->intereses = $e->intereses;
        $this->activo = $e->activo;
    }

    public function update()
    {
        $this->validate();

        $e = Estudiante::findOrFail($this->estudianteId);

        // Update user
        $user = $e->user;
        $user->name = $this->name;
        $user->apellido = $this->apellido;
        $user->email = $this->email;
        $user->telefono = $this->telefono;
        $user->save();

        // Update estudiante
        $e->matricula = $this->matricula;
        $e->fecha_nacimiento = $this->fecha_nacimiento;
        $e->genero = $this->genero;
        $e->nivel_educativo = $this->nivel_educativo;
        $e->intereses = $this->intereses;
        $e->activo = $this->activo;
        $e->save();

        session()->flash('message', 'Estudiante actualizado correctamente.');
        return redirect()->route('admin.estudiantes.index');
    }

    public function render()
    {
        return view('livewire.estudiante.editar-estudiante');
    }
}
