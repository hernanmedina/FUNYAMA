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

    /**
     * Devuelve el contenido listo para renderizar en la vista de detalle.
     *
     * Si el contenido contiene etiquetas HTML, se sanitiza con HTMLPurifier
     * para permitir formato enriquecido de forma segura. Si es texto plano,
     * se convierten los saltos de línea en párrafos.
     */
    public function getContenidoHtmlAttribute(): string
    {
        $contenido = (string) $this->contenido;

        if ($contenido === '') {
            return '';
        }

        if ($contenido !== strip_tags($contenido)) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Cache.SerializerPath', storage_path('app/htmlpurifier'));
            $config->set('HTML.Allowed', 'p,br,strong,b,em,i,u,ul,ol,li,h2,h3,h4,blockquote,a[href],img[src|alt],code,pre,hr,table,thead,tbody,tr,th,td');
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true]);

            return (new \HTMLPurifier($config))->purify($contenido);
        }

        $parrafos = preg_split('/\n\s*\n/', trim($contenido));

        return collect($parrafos)
            ->map(fn ($parrafo) => '<p>'.nl2br(e(trim($parrafo))).'</p>')
            ->implode("\n");
    }

    public function incrementarVistas()
    {
        $this->increment('vistas');
    }
}
