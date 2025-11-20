<?php

namespace App\Livewire\Admin\Cursos;

use Livewire\Component;
use App\Models\Curso;
use App\Models\Administrador;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class CrearCurso extends Component
{
    use WithFileUploads;

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
    public $nivel = 'principiante';
    public $imagen_portada;
    public $fecha_inicio;
    public $publicado = false;
    public $destacado = false;

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
        'fecha_inicio' => 'nullable|date|after_or_equal:today',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
    ];

    protected $messages = [
        'precio_descuento.lt' => 'El precio de descuento debe ser menor al precio regular.',
        'fecha_inicio.after_or_equal' => 'La fecha de inicio debe ser hoy o una fecha futura.',
    ];

    public function updatedNombre($value)
    {
        // Generar slug automáticamente
        $this->slug = Str::slug($value);
    }

    public function guardarCurso()
    {
        try {
            \Log::info('=== INICIANDO CREACIÓN DE CURSO ===');

            // Validar
            $validatedData = $this->validate();
            \Log::info('✅ Validación pasada', $validatedData);

            // Obtener administrador
            $admin = Administrador::where('user_id', Auth::id())->first();

            if (!$admin) {
                \Log::error('❌ No se encontró administrador para el usuario: ' . Auth::id());
                session()->flash('error', 'No tienes permisos de administrador para crear cursos.');
                return;
            }

            \Log::info('✅ Administrador encontrado: ' . $admin->idAdmin);

            // Procesar imagen
            $imagenPath = null;
            if ($this->imagen_portada) {
                $imagenPath = $this->imagen_portada->store('cursos', 'public');
                \Log::info('✅ Imagen guardada: ' . $imagenPath);
            }

            \Log::info('🔄 Creando curso...');

            $curso = Curso::create([
                'nombre' => $this->nombre,
                'slug' => Str::slug($this->nombre),
                'descripcion' => $this->descripcion,
                'cronograma' => $this->cronograma,
                'requisitos' => $this->requisitos,
                'objetivos' => $this->objetivos,
                'materiales_incluidos' => $this->materiales_incluidos,
                'cupo_total' => $this->cupo_total,
                'cupo_disponible' => $this->cupo_total,
                'duracion_horas' => $this->duracion_horas,
                'duracion_texto' => $this->duracion_texto,
                'precio_regular' => $this->precio_regular,
                'precio_descuento' => $this->precio_descuento,
                'nivel' => $this->nivel,
                'imagen_portada' => $imagenPath,
                'fecha_inicio' => $this->fecha_inicio,
                'publicado' => $this->publicado,
                'destacado' => $this->destacado,
                'creado_por_admin' => $admin->idAdmin,
            ]);

            \Log::info('🎉 Curso creado exitosamente: ' . $curso->idCurso);

            // Limpiar el formulario
            $this->reset();

            // Mostrar mensaje de éxito y redirigir
            session()->flash('success', 'Curso creado exitosamente!');
            return redirect()->route('admin.cursos.index');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('❌ Error de validación:', $e->errors());
            // Los errores de validación se mostrarán automáticamente
            throw $e;

        } catch (\Exception $e) {
            \Log::error('❌ Error al crear curso: ' . $e->getMessage());
            session()->flash('error', 'Error al crear el curso: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.cursos.crear-curso')
            ->layout('layouts.app');
    }
}
