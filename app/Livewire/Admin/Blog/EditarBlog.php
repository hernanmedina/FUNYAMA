<?php

namespace App\Livewire\Admin\Blog;

use App\Models\Articulo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class EditarBlog extends Component
{
    use WithFileUploads;

    public Articulo $articulo;

    public string $titulo = '';

    public string $slug = '';

    public string $resumen = '';

    public string $contenido = '';

    public $imagen_portada;

    public ?string $imagen_portada_temp = null;

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
            'slug' => 'required|string|max:255|unique:articulos,slug,'.$this->articulo->idPost.',idPost',
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

    public function mount(Articulo $articulo): void
    {
        $this->authorize('update', $articulo);

        $this->articulo = $articulo;

        $this->titulo = $articulo->titulo;
        $this->slug = $articulo->slug;
        $this->resumen = $articulo->resumen;
        $this->contenido = $articulo->contenido;
        $this->imagen_portada_temp = $articulo->imagen_portada;
        $this->categoria = $articulo->categoria;
        $this->etiquetas = is_array($articulo->etiquetas) ? $articulo->etiquetas : (json_decode($articulo->etiquetas ?? '', true) ?? []);
        $this->autor = $articulo->autor;
        $this->fuente = $articulo->fuente ?? '';
        $this->tiempo_lectura = $articulo->tiempo_lectura ?? 5;
        $this->publicado = (bool) $articulo->publicado;
        $this->destacado = (bool) $articulo->destacado;
        $this->comentarios_habilitados = (bool) $articulo->comentarios_habilitados;
    }

    public function updatedTitulo(string $value): void
    {
        if (empty($this->slug) || $this->slug === Str::slug($this->articulo->titulo)) {
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

    public function actualizar()
    {
        $this->authorize('update', $this->articulo);

        $this->validate();

        $imagenPath = $this->imagen_portada_temp;

        if ($this->imagen_portada) {
            if ($this->imagen_portada_temp && Storage::disk('public')->exists($this->imagen_portada_temp)) {
                Storage::disk('public')->delete($this->imagen_portada_temp);
            }
            $imagenPath = $this->imagen_portada->store('blog', 'public');
        }

        $publicadoAntes = $this->articulo->publicado;

        $this->articulo->update([
            'titulo' => $this->titulo,
            'slug' => $this->slug,
            'resumen' => $this->resumen,
            'contenido' => $this->contenido,
            'imagen_portada' => $imagenPath,
            'categoria' => $this->categoria,
            'etiquetas' => $this->etiquetas,
            'autor' => $this->autor,
            'fuente' => $this->fuente,
            'tiempo_lectura' => $this->tiempo_lectura ?: null,
            'publicado' => $this->publicado,
            'destacado' => $this->destacado,
            'comentarios_habilitados' => $this->comentarios_habilitados,
            'fecha_publicacion' => $this->publicado && ! $publicadoAntes ? now() : $this->articulo->fecha_publicacion,
        ]);

        $this->dispatch('show-toast', type: 'success', message: 'Artículo actualizado exitosamente.');

        return redirect()->route('admin.blog.index');
    }

    public function render()
    {
        return view('livewire.admin.blog.editar-blog')
            ->layout('layouts.app');
    }
}
