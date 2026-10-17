<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CursoRecurso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'curso_recursos';

    protected $fillable = [
        'curso_id',
        'titulo',
        'descripcion',
        'tipo',
        'archivo_path',
        'enlace',
        'version',
        'publicado',
    ];

    protected $casts = [
        'publicado' => 'boolean',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'codigo');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function getTipoEtiquetaAttribute(): string
    {
        return match ($this->tipo) {
            'plantilla' => 'Plantilla',
            'guia' => 'Guía',
            default => 'Recurso',
        };
    }
}
