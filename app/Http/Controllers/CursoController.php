<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use Illuminate\Http\Request;

class CursoController extends Controller
{
    /**
     * No usamos index aquí porque lo maneja Livewire (Cursos::class)
     */

    /**
     * Tampoco usamos create porque lo maneja Livewire (CrearCurso::class)
     */

    public function store(Request $request)
    {
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cronograma' => 'required|string',
            'requisitos' => 'required|string',
            'cupo' => 'required|integer|min:1',
        ]);

        // Agregar el usuario que crea el curso
        $validated['creadoPorAdmin'] = auth()->id();

        // Crear curso
        Curso::create($validated);

        // Redirigir al listado Livewire
        return redirect()->route('cursos.index')       //
            ->with('success', 'Curso creado exitosamente.');
    }


    public function show(Curso $curso)
    {
        // Puedes usar Livewire o una vista
        return view('cursos.show', compact('curso'));
    }


    public function edit(Curso $curso)
    {
        // Puedes usar Livewire o una vista Blade
        return view('cursos.edit', compact('curso'));
    }


    public function update(Request $request, Curso $curso)
    {
        // Validación
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'cronograma' => 'required|string',
            'requisitos' => 'required|string',
            'cupo' => 'required|integer|min:1',

        ]);

        // Actualizar
        $curso->update($validated);

        return redirect()->route('cursos.index')
            ->with('success', 'Curso actualizado correctamente.');
    }


    public function destroy(Curso $curso)
    {
        $curso->delete();

        return redirect()->route('cursos.index')
            ->with('success', 'Curso eliminado correctamente.');
    }
}
