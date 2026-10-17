<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CursoNetworking extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'curso_networking';

    protected $fillable = [
        'curso_id',
        'estudiante_id',
        'mensaje',
        'tipo',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'codigo');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'codigo');
    }

    public function getTipoEtiquetaAttribute(): string
    {
        return match ($this->tipo) {
            'presentacion' => 'Presentación',
            'colaboracion' => 'Colaboración',
            default => 'Mensaje',
        };
    }
}
