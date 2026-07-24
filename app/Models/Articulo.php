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
        'autor_id_admin'
    ];

    protected $casts = [
        'etiquetas' => 'array',
        'publicado' => 'boolean',
        'destacado' => 'boolean',
        'comentarios_habilitados' => 'boolean',
        'fecha_publicacion' => 'datetime',
        'vistas' => 'integer',
        'likes' => 'integer',
        'tiempo_lectura' => 'integer'
    ];

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'autor_id_admin', 'idAdmin');
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
