<?php

namespace App\Livewire\Estudiante;

use Livewire\Component;
use App\Models\User;
use App\Models\Estudiante;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')] // Agregar el layout
class CrearEstudiante extends Component
{
    public $name;
    public $apellido;
    public $email;
    public $password;
    public $telefono;

    public $codigo;
    public $fecha_nacimiento;
    public $genero;
    public $nivel_educativo;
    public $intereses;
    public $activo = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'apellido' => 'nullable|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'telefono' => 'nullable|string|max:30',

        'codigo' => 'required|string|max:100|unique:estudiantes,codigo',
        'fecha_nacimiento' => 'nullable|date',
        'genero' => 'nullable|in:masculino,femenino,otro',
        'nivel_educativo' => 'nullable|string|max:255',
        'intereses' => 'nullable|string',
        'activo' => 'boolean',
    ];

    public function store()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $this->name,
                'apellido' => $this->apellido,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'telefono' => $this->telefono,
                'role' => 'estu',
            ]);

            Estudiante::create([
                'codigo' => $this->codigo,
                'user_id' => $user->id,
                'fecha_nacimiento' => $this->fecha_nacimiento,
                'genero' => $this->genero,
                'nivel_educativo' => $this->nivel_educativo,
                'intereses' => $this->intereses,
                'fecha_registro' => now(),
                'activo' => $this->activo,
            ]);

            DB::commit();

            session()->flash('message', 'Estudiante creado correctamente.');
            return redirect()->route('admin.estudiantes.index');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('general', 'Error al crear estudiante: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.estudiante.crear-estudiante');
    }
}
