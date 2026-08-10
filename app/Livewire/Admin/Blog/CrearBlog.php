<?php

namespace App\Livewire\Admin\Blog;

use App\Models\Administrador;
use App\Models\Articulo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CrearBlog extends Component
{
    use WithFileUploads;

    public string $titulo = '';

    public string $slug = '';

    public string $resumen = '';

    public string $contenido = '';

    public $imagen_portada;

    public string $categoria = 'general';

    public array $etiquetas = [];

    public string $nuevaEtiqueta = '';

    public string $autor = '';

    public string $fuente = '';

    public int $tiempo_lectura = 5;

    public bool $publicado = false;

    public bool $destacado = false;

    public bool $comentarios_habilitados = true;

    protected function rules(): array
    {
        return [
            'titulo' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:articulos,slug',
            'resumen' => 'required|string|min:20|max:500',
            'contenido' => 'required|string|min:50',
            'imagen_portada' => 'nullable|image|max:3072',
            'categoria' => 'required|string|max:100',
            'etiquetas' => 'nullable|array',
            'autor' => 'nullable|string|max:255',
            'fuente' => 'nullable|string|max:255',
            'tiempo_lectura' => 'nullable|integer|min:1',
            'publicado' => 'boolean',
            'destacado' => 'boolean',
            'comentarios_habilitados' => 'boolean',
        ];
    }

    public function updatedTitulo(string $value): void
    {
        if (empty($this->slug) || $this->slug === Str::slug($this->getOriginalTitulo())) {
            $this->slug = Str::slug($value);
        }
    }

    public function agregarEtiqueta(): void
    {
        $etiqueta = trim($this->nuevaEtiqueta);

        if ($etiqueta !== '' && ! in_array($etiqueta, $this->etiquetas, true)) {
            $this->etiquetas[] = $etiqueta;
        }

        $this->nuevaEtiqueta = '';
    }

    public function quitarEtiqueta(int $index): void
    {
        unset($this->etiquetas[$index]);
        $this->etiquetas = array_values($this->etiquetas);
    }

    public function store()
    {
        $this->validate();

        $admin = Administrador::where('user_id', Auth::id())->first();

        if (! $admin) {
            session()->flash('error', 'No tienes permisos para publicar artículos.');

            return;
        }

        $imagenPath = null;
        if ($this->imagen_portada) {
            $imagenPath = $this->imagen_portada->store('blog', 'public');
        }

        $autorNombre = $this->autor ?: Auth::user()->nombre_completo;

        Articulo::create([
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'contenido' => $this->contenido,
            'imagen_portada' => $imagenPath,
            'categoria' => $this->categoria,
            'etiquetas' => $this->etiquetas,
            'autor' => $autorNombre,
            'fuente' => $this->fuente,
            'tiempo_lectura' => $this->tiempo_lectura ?: null,
            'publicado' => $this->publicado,
            'destacado' => $this->destacado,
            'comentarios_habilitados' => $this->comentarios_habilitados,
            'fecha_publicacion' => $this->publicado ? now() : null,
            'autor_id_admin' => $admin->idAdmin,
        ]);

        session()->flash('success', '¡Artículo creado exitosamente!');

        return redirect()->route('admin.blog.index');
    }

    private function getOriginalTitulo(): string
    {
        return '';
    }

    public function render()
    {
        return view('livewire.admin.blog.crear-blog')
            ->layout('layouts.app');
    }
}
