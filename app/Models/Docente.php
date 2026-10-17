<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Docente extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'docentes';

    protected $primaryKey = 'idDocente';

    protected $fillable = [
        'user_id',
        'especialidad',
        'biografia',
        'foto',
        'telefono_contacto',
        'fecha_ingreso',
        'activo',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'activo' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'instructor_id', 'user_id');
    }
}
