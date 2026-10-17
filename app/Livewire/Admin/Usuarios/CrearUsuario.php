<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\Administrador;
use App\Models\Docente;
use App\Models\User;
use App\Notifications\InvitacionUsuario;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class CrearUsuario extends Component
{
    use WithFileUploads;

    // ─── Datos comunes ───
    public string $rol = 'instructor';

    public string $name = '';

    public string $apellido = '';

    public string $email = '';

    public string $documento_ID = '';

    public string $telefono = '';

    public string $direccion = '';

    // ─── Datos de administrador ───
    public string $departamento = '';

    public string $cargo = '';

    public bool $super_admin = false;

    // ─── Datos de docente ───
    public string $especialidad = '';

    public string $biografia = '';

    public $foto;

    public string $telefono_contacto = '';

    public bool $activo = true;

    protected function rules(): array
    {
        $reglas = [
            'rol' => ['required', 'in:admin,instructor'],
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'documento_ID' => ['required', 'string', 'max:20', 'unique:users,documento_ID'],
            'telefono' => ['required', 'string', 'max:30'],
            'direccion' => ['required', 'string', 'max:255'],
        ];

        if ($this->rol === 'admin') {
            $reglas['departamento'] = ['nullable', 'string', 'max:255'];
            $reglas['cargo'] = ['nullable', 'string', 'max:255'];
            $reglas['super_admin'] = ['boolean'];
        }

        if ($this->rol === 'instructor') {
            $reglas['especialidad'] = ['nullable', 'string', 'max:255'];
            $reglas['biografia'] = ['nullable', 'string', 'max:2000'];
            $reglas['foto'] = ['nullable', 'image', 'max:2048'];
            $reglas['telefono_contacto'] = ['nullable', 'string', 'max:30'];
            $reglas['activo'] = ['boolean'];
        }

        return $reglas;
    }

    public function updatedRol(): void
    {
        $this->resetValidation();
    }

    public function store()
    {
        $this->authorize('create', User::class);

        $this->validate();

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $this->name,
                'apellido' => $this->apellido ?: null,
                'documento_ID' => $this->documento_ID ?: null,
                'email' => $this->email,
                // Contraseña aleatoria: el usuario la establecerá vía invitación.
                'password' => bcrypt(Str::random(32)),
                'role' => $this->rol,
                'telefono' => $this->telefono ?: null,
                'direccion' => $this->direccion ?: null,
            ]);

            if ($this->rol === 'admin') {
                Administrador::create([
                    'user_id' => $user->id,
                    'departamento' => $this->departamento ?: null,
                    'cargo' => $this->cargo ?: null,
                    'super_admin' => $this->super_admin,
                    'fecha_ingreso' => now(),
                ]);
            }

            if ($this->rol === 'instructor') {
                $fotoPath = $this->foto
                    ? $this->foto->store('docentes', 'public')
                    : null;

                Docente::create([
                    'user_id' => $user->id,
                    'especialidad' => $this->especialidad ?: null,
                    'biografia' => $this->biografia ?: null,
                    'foto' => $fotoPath,
                    'telefono_contacto' => $this->telefono_contacto ?: null,
                    'fecha_ingreso' => now(),
                    'activo' => $this->activo,
                ]);
            }

            DB::commit();

            $this->enviarInvitacion($user);

            session()->flash('success', 'Usuario creado correctamente. Se envió una invitación a '.$user->email.'.');

            return redirect()->route('admin.usuarios.index');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->addError('general', 'Error al crear el usuario: '.$e->getMessage());
        }
    }

    /**
     * Envía la invitación por email con el enlace para establecer la contraseña.
     */
    private function enviarInvitacion(User $user): void
    {
        $token = Password::broker()->createToken($user);

        $user->notify(new InvitacionUsuario(
            $user->getNombreCompletoAttribute(),
            $user->role,
            $token,
        ));
    }

    public function render()
    {
        return view('livewire.admin.usuarios.crear-usuario');
    }
}
