<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $fillable = [
        'curso_id',
        'estudiante_id',
        'fecha',
        'estado',
        'observacion',
        'registrado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'codigo');
    }

    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'codigo');
    }

    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    public function getEstadoEtiquetaAttribute(): string
    {
        return match ($this->estado) {
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'tarde' => 'Tarde',
            'justificado' => 'Justificado',
            default => 'Desconocido',
        };
    }
}
