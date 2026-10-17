<?php

namespace App\Livewire\Admin\Usuarios;

use App\Models\Administrador;
use App\Models\Docente;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EditarUsuario extends Component
{
    use WithFileUploads;

    public User $usuario;

    // ─── Datos comunes ───
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

    public ?string $fotoActual = null;

    public string $telefono_contacto = '';

    public bool $activo = true;

    public function mount(User $usuario): void
    {
        $this->authorize('update', $usuario);

        $this->usuario = $usuario;

        $this->name = $usuario->name ?? '';
        $this->apellido = $usuario->apellido ?? '';
        $this->email = $usuario->email ?? '';
        $this->documento_ID = $usuario->documento_ID ?? '';
        $this->telefono = $usuario->telefono ?? '';
        $this->direccion = $usuario->direccion ?? '';

        if ($usuario->isAdmin() && $usuario->administrador) {
            $this->departamento = $usuario->administrador->departamento ?? '';
            $this->cargo = $usuario->administrador->cargo ?? '';
            $this->super_admin = (bool) $usuario->administrador->super_admin;
        }

        if ($usuario->isInstructor() && $usuario->docente) {
            $this->especialidad = $usuario->docente->especialidad ?? '';
            $this->biografia = $usuario->docente->biografia ?? '';
            $this->fotoActual = $usuario->docente->foto;
            $this->telefono_contacto = $usuario->docente->telefono_contacto ?? '';
            $this->activo = (bool) $usuario->docente->activo;
        }
    }

    protected function rules(): array
    {
        $reglas = [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$this->usuario->id],
            'documento_ID' => ['required', 'string', 'max:20', 'unique:users,documento_ID,'.$this->usuario->id],
            'telefono' => ['required', 'string', 'max:30'],
            'direccion' => ['required', 'string', 'max:255'],
        ];

        if ($this->usuario->isAdmin()) {
            $reglas['departamento'] = ['nullable', 'string', 'max:255'];
            $reglas['cargo'] = ['nullable', 'string', 'max:255'];
            $reglas['super_admin'] = ['boolean'];
        }

        if ($this->usuario->isInstructor()) {
            $reglas['especialidad'] = ['nullable', 'string', 'max:255'];
            $reglas['biografia'] = ['nullable', 'string', 'max:2000'];
            $reglas['foto'] = ['nullable', 'image', 'max:2048'];
            $reglas['telefono_contacto'] = ['nullable', 'string', 'max:30'];
            $reglas['activo'] = ['boolean'];
        }

        return $reglas;
    }

    public function update()
    {
        $this->authorize('update', $this->usuario);

        $this->validate();

        DB::beginTransaction();

        try {
            $this->usuario->update([
                'name' => $this->name,
                'apellido' => $this->apellido ?: null,
                'documento_ID' => $this->documento_ID ?: null,
                'email' => $this->email,
                'telefono' => $this->telefono ?: null,
                'direccion' => $this->direccion ?: null,
            ]);

            if ($this->usuario->isAdmin()) {
                Administrador::updateOrCreate(
                    ['user_id' => $this->usuario->id],
                    [
                        'departamento' => $this->departamento ?: null,
                        'cargo' => $this->cargo ?: null,
                        'super_admin' => $this->super_admin,
                    ]
                );
            }

            if ($this->usuario->isInstructor()) {
                $fotoPath = $this->fotoActual;

                if ($this->foto) {
                    $fotoPath = $this->foto->store('docentes', 'public');
                }

                Docente::updateOrCreate(
                    ['user_id' => $this->usuario->id],
                    [
                        'especialidad' => $this->especialidad ?: null,
                        'biografia' => $this->biografia ?: null,
                        'foto' => $fotoPath,
                        'telefono_contacto' => $this->telefono_contacto ?: null,
                        'activo' => $this->activo,
                    ]
                );
            }

            DB::commit();

            session()->flash('success', 'Usuario actualizado correctamente.');

            return redirect()->route('admin.usuarios.index');
        } catch (\Exception $e) {
            DB::rollBack();

            $this->addError('general', 'Error al actualizar el usuario: '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.usuarios.editar-usuario');
    }
}
