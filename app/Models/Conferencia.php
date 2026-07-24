<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conferencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'conferencias';
    protected $primaryKey = 'idConferencia';

    protected $fillable = [
        'titulo',
        'slug',
        'descripcion',
        'temario',
        'fecha',
        'hora_inicio',
        'hora_fin',
        'ponente',
        'bio_ponente',
        'foto_ponente',
        'modalidad',
        'enlace',
        'lugar',
        'cupo_maximo',
        'inscritos_actual',
        'costo',
        'nivel',
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
    public function scopeProximas($query)
    {
        return $query->where('fecha', '>=', now())->orderBy('fecha', 'asc');
    }

    public function scopeVirtuales($query)
    {
        return $query->where('modalidad', 'virtual');
    }

    public function scopePresenciales($query)
    {
        return $query->where('modalidad', 'presencial');
    }

    // Helpers
    public function getCuposDisponiblesAttribute()
    {
        return $this->cupo_maximo - $this->inscritos_actual;
    }

    public function getEsVirtualAttribute()
    {
        return $this->modalidad === 'virtual';
    }
}
