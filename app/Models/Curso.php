<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Curso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cursos';

    protected $primaryKey = 'codigo';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'codigo',
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
        'enlace_classroom',
        'temario',
        'publicado',
        'destacado',
        'comunidad_habilitada',
        'comunidad_tras_finalizar',
        'fecha_inicio',
        'fecha_fin',
        'creado_por_admin',
        'instructor_id',
    ];

    protected $casts = [
        'precio_regular' => 'decimal:2',
        'precio_descuento' => 'decimal:2',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'comunidad_habilitada' => 'boolean',
        'comunidad_tras_finalizar' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'cupo_total' => 'integer',
        'cupo_disponible' => 'integer',
    ];

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'creado_por_admin', 'idAdmin');
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function estudiantes()
    {
        return $this->belongsToMany(Estudiante::class, 'curso_estudiante', 'curso_id', 'estudiante_id')
            ->withPivot('estado', 'calificacion', 'comentario_calificacion', 'rating_estudiante', 'opinion_estudiante', 'pago_realizado', 'estado_pago', 'progreso', 'temario_progreso', 'fecha_inscripcion')
            ->withTimestamps();
    }

    public function certificados()
    {
        return $this->hasMany(Certificado::class, 'curso_id', 'codigo');
    }

    public function preguntas()
    {
        return $this->hasMany(CursoPregunta::class, 'curso_id', 'codigo');
    }

    public function recursos()
    {
        return $this->hasMany(CursoRecurso::class, 'curso_id', 'codigo');
    }

    public function publicacionesNetworking()
    {
        return $this->hasMany(CursoNetworking::class, 'curso_id', 'codigo');
    }

    public function asistencias()
    {
        return $this->hasMany(Asistencia::class, 'curso_id', 'codigo');
    }

    /**
     * Indica si la comunidad está disponible para un estudiante según su estado
     * de inscripción. El administrador decide si se mantiene tras finalizar.
     */
    public function comunidadDisponiblePara(?string $estadoInscripcion): bool
    {
        if (! $this->comunidad_habilitada) {
            return false;
        }

        if ($estadoInscripcion === 'completado') {
            return (bool) $this->comunidad_tras_finalizar;
        }

        return true;
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

    public function getInscritosActualesAttribute()
    {
        if (isset($this->attributes['estudiantes_count'])) {
            return (int) $this->attributes['estudiantes_count'];
        }

        return max(0, $this->cupo_total - $this->cupo_disponible);
    }

    public function getPrecioFinalAttribute()
    {
        return $this->precio_descuento ?? $this->precio_regular;
    }

    public function getEstaDisponibleAttribute()
    {
        return $this->cupo_disponible > 0 && $this->publicado;
    }

    public function getTemarioItemsAttribute()
    {
        if (empty($this->temario)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->temario)), fn ($item) => $item !== ''));
    }
}
