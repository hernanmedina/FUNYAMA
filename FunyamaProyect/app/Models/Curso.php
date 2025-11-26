<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cursos';
    protected $primaryKey = 'idCurso';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'cronograma',
        'requisitos',
        'objetivos',
        'materiales_incluidos',
        'cupo_total',
        'cupo_disponible',
        'duracion_horas',
        'duracion_texto',
        'precio_regular',
        'precio_descuento',
        'nivel',
        'imagen_portada',
        'video_presentacion',
        'publicado',
        'destacado',
        'fecha_inicio',
        'fecha_fin',
        'creado_por_admin'
    ];

    protected $casts = [
        'precio_regular' => 'decimal:2',
        'precio_descuento' => 'decimal:2',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'cupo_total' => 'integer',
        'cupo_disponible' => 'integer'
    ];

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'creado_por_admin', 'idAdmin');
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'curso_estudiante', 'curso_id', 'estudiante_id')
            ->withPivot('estado', 'calificacion', 'pago_realizado', 'estado_pago', 'progreso', 'fecha_inscripcion')
            ->withTimestamps();
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'curso_id', 'idCurso');
    }

    // Scopes
    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true);
    }

    public function scopeActivos($query)
    {
        return $query->whereNull('deleted_at');
    }

    // Helpers
    public function getCuposDisponiblesAttribute()
    {
        return $this->cupo_disponible;
    }

    public function getPrecioFinalAttribute()
    {
        return $this->precio_descuento ?? $this->precio_regular;
    }

    public function getEstaDisponibleAttribute()
    {
        return $this->cupo_disponible > 0 && $this->publicado;
    }
}
