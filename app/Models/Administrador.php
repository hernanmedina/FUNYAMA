<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Administrador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'administradores';
    protected $primaryKey = 'idAdmin';

    protected $fillable = [
        'user_id',
        'departamento',
        'cargo',
        'telefono_contacto',
        'permisos',
        'super_admin',
        'fecha_ingreso'
    ];

    protected $casts = [
        'super_admin' => 'boolean',
        'permisos' => 'array',
        'fecha_ingreso' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cursos()
    {
        return $this->hasMany(Curso::class, 'creado_por_admin');
    }

    public function eventos()
    {
        return $this->hasMany(Evento::class, 'creado_por_admin');
    }

    public function conferencias()
    {
        return $this->hasMany(Conferencia::class, 'creado_por_admin');
    }

    public function articulos()
    {
        return $this->hasMany(Articulo::class, 'autor_id_admin');
    }
}
