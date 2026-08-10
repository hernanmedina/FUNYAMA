<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articulos';

    protected $primaryKey = 'idPost';

    protected $fillable = [
        'titulo',
        'slug',
        'resumen',
        'contenido',
        'imagen_portada',
        'categoria',
        'etiquetas',
        'autor',
        'fuente',
        'vistas',
        'likes',
        'tiempo_lectura',
        'publicado',
        'destacado',
        'comentarios_habilitados',
        'fecha_publicacion',
        'autor_id_admin',
    ];

    protected $casts = [
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'comentarios_habilitados' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'vistas' => 'integer',
        'likes' => 'integer',
        'tiempo_lectura' => 'integer',
    ];

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'autor_id_admin', 'idAdmin');
    }

    protected function getEtiquetasAttribute($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }

        $decoded = is_array($value) ? $value : json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    protected function setEtiquetasAttribute(array|string|null $value): void
    {
        if (is_array($value)) {
            $this->attributes['etiquetas'] = json_encode($value, JSON_UNESCAPED_UNICODE);
        } else {
            $this->attributes['etiquetas'] = $value;
        }
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

    public function scopeRecientes($query)
    {
        return $query->orderBy('fecha_publicacion', 'desc');
    }

    // Helpers
    public function getEstaPublicadoAttribute()
    {
        return $this->publicado && $this->fecha_publicacion <= now();
    }

    public function incrementarVistas()
    {
        $this->increment('vistas');
    }
}
