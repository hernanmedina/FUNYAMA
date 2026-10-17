<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CursoPregunta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'curso_preguntas';

    protected $fillable = [
        'curso_id',
        'estudiante_id',
        'titulo',
        'contenido',
        'resuelta',
        'respuesta_destacada',
    ];

    protected $casts = [
        'resuelta' => 'boolean',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'codigo');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'codigo');
    }

    public function respuestas()
    {
        return $this->hasMany(CursoRespuesta::class, 'pregunta_id')
            ->orderByDesc('es_destacada')
            ->orderBy('created_at');
    }
}
