<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Estudiante extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'estudiantes';
    protected $primaryKey = 'idEstudiante';

    protected $fillable = [
        'user_id',
        'matricula',
        'fecha_nacimiento',
        'genero',
        'nivel_educativo',
        'intereses',
        'fecha_registro',
        'activo'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_registro' => 'date',
        'activo' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cursos()
    {
        return $this->belongsToMany(Curso::class, 'curso_estudiante', 'estudiante_id', 'curso_id')
            ->withPivot('estado', 'calificacion', 'pago_realizado', 'estado_pago', 'progreso', 'fecha_inscripcion')
            ->withTimestamps();
    }

    public function solicitudes()
    {
        return $this->hasMany(Solicitud::class, 'user_id', 'user_id');
    }
}
