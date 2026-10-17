<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CursoRespuesta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'curso_respuestas';

    protected $fillable = [
        'pregunta_id',
        'estudiante_id',
        'contenido',
        'es_destacada',
    ];

    protected $casts = [
        'es_destacada' => 'boolean',
    ];

    public function pregunta()
    {
        return $this->belongsTo(CursoPregunta::class, 'pregunta_id');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'codigo');
    }
}
