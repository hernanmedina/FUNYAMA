<?php

namespace App\Livewire\Docente;

use App\Models\Curso;
use App\Models\CursoRecurso;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class GestionarRecursosDocente extends Component
{
    use WithFileUploads, WithPagination;

    public Curso $curso;

    public bool $showModal = false;

    public ?int $recursoEditandoId = null;

    public string $titulo = '';

    public string $descripcion = '';

    public string $tipo = 'recurso';

    public string $enlace = '';

    public string $version = '1.0';

    public bool $publicado = true;

    public $archivo = null;

    public string $filtroTipo = 'todos';

    public string $search = '';

    public function mount(Curso $curso): void
    {
        $this->authorize('view', $curso);
        $this->curso = $curso;
    }

    public function updatedFiltroTipo(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function recursos()
    {
        $query = CursoRecurso::query()->where('curso_id', $this->curso->codigo);

        if ($this->filtroTipo !== 'todos') {
            $query->where('tipo', $this->filtroTipo);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%'.$this->search.'%')
                    ->orWhere('descripcion', 'like', '%'.$this->search.'%');
            });
        }

        return $query->latest()->paginate(10);
    }

    public function abrirModal(): void
    {
        $this->authorize('create', CursoRecurso::class);

        $this->reset(['recursoEditandoId', 'titulo', 'descripcion', 'enlace', 'archivo']);
        $this->tipo = 'recurso';
        $this->version = '1.0';
        $this->publicado = true;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function editarRecurso(int $recursoId): void
    {
        $recurso = CursoRecurso::findOrFail($recursoId);
        $this->authorize('update', $recurso);

        $this->recursoEditandoId = $recurso->id;
        $this->titulo = $recurso->titulo;
        $this->descripcion = $recurso->descripcion ?? '';
        $this->tipo = $recurso->tipo;
        $this->enlace = $recurso->enlace ?? '';
        $this->version = $recurso->version;
        $this->publicado = $recurso->publicado;
        $this->archivo = null;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function cerrarModal(): void
    {
        $this->showModal = false;
        $this->reset(['recursoEditandoId', 'titulo', 'descripcion', 'enlace', 'archivo']);
        $this->resetValidation();
    }

    public function guardar(): void
    {
        $this->validate([
            'titulo' => ['required', 'string', 'min:3', 'max:150'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'tipo' => ['required', 'in:plantilla,guia,recurso'],
            'enlace' => ['nullable', 'url', 'max:255'],
            'version' => ['required', 'string', 'max:20'],
            'publicado' => ['boolean'],
            'archivo' => ['nullable', 'file', 'max:20480'],
        ], [], [
            'titulo' => 'título',
            'descripcion' => 'descripción',
            'enlace' => 'enlace',
            'version' => 'versión',
        ]);

        if ($this->recursoEditandoId) {
            $recurso = CursoRecurso::findOrFail($this->recursoEditandoId);
            $this->authorize('update', $recurso);
        } else {
            $this->authorize('create', CursoRecurso::class);
            $recurso = new CursoRecurso;
            $recurso->curso_id = $this->curso->codigo;
        }

        if ($this->archivo) {
            if ($recurso->archivo_path) {
                Storage::disk('public')->delete($recurso->archivo_path);
            }

            $recurso->archivo_path = $this->archivo->store('recursos-cursos', 'public');
        }

        $recurso->titulo = $this->titulo;
        $recurso->descripcion = $this->descripcion ?: null;
        $recurso->tipo = $this->tipo;
        $recurso->enlace = $this->enlace ?: null;
        $recurso->version = $this->version;
        $recurso->publicado = $this->publicado;
        $recurso->save();

        $this->cerrarModal();
        unset($this->recursos);

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: $this->recursoEditandoId ? 'Recurso actualizado.' : 'Recurso agregado al curso.'
        );
    }

    public function togglePublicado(int $recursoId): void
    {
        $recurso = CursoRecurso::findOrFail($recursoId);
        $this->authorize('update', $recurso);

        $recurso->update(['publicado' => ! $recurso->publicado]);

        unset($this->recursos);

        $this->dispatch(
            'show-toast',
            type: 'success',
            message: $recurso->publicado ? 'Recurso publicado.' : 'Recurso oculto para los estudiantes.'
        );
    }

    public function eliminarRecurso(int $recursoId): void
    {
        $recurso = CursoRecurso::findOrFail($recursoId);
        $this->authorize('delete', $recurso);

        if ($recurso->archivo_path) {
            Storage::disk('public')->delete($recurso->archivo_path);
        }

        $recurso->delete();

        unset($this->recursos);

        $this->dispatch('show-toast', type: 'success', message: 'Recurso eliminado.');
    }

    public function render()
    {
        return view('livewire.docente.gestionar-recursos-docente');
    }
}
