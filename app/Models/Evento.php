<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eventos';
    protected $primaryKey = 'idEvento';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'contenido',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'ubicacion',
        'direccion',
        'ciudad',
        'imagen',
        'cupo_maximo',
        'inscritos_actual',
        'costo',
        'tipo_evento',
        'enlace_virtual',
        'publicado',
        'destacado',
        'creado_por_admin'
    ];

    protected $casts = [
        'fecha' => 'date',
        'costo' => 'decimal:2',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'cupo_maximo' => 'integer',
        'inscritos_actual' => 'integer'
    ];

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'creado_por_admin', 'idAdmin');
    }

    // Scopes
    public function scopeProximos($query)
    {
        return $query->where('fecha', '>=', now())->orderBy('fecha', 'asc');
    }

    public function scopePublicados($query)
    {
        return $query->where('publicado', true);
    }

    // Helpers
    public function getCuposDisponiblesAttribute()
    {
        return $this->cupo_maximo - $this->inscritos_actual;
    }

    public function getEsGratuitoAttribute()
    {
        return $this->costo == 0;
    }
}
