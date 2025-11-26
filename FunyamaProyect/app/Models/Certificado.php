<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificado extends Model
{
    use HasFactory;

    protected $table = 'certificados';
    protected $primaryKey = 'id';

    protected $fillable = [
        'estudiante_id',
        'curso_id',
        'numero_certificado',
        'fecha_emision',
        'calificacion_final',
        'archivo_path',
        'descargas',
        'ultima_descarga',
    ];

    protected $casts = [
        'fecha_emision' => 'date',
        'ultima_descarga' => 'datetime',
        'calificacion_final' => 'decimal:1',
    ];

    // Relaciones
    public function estudiante()
    {
        return $this->belongsTo(Estudiante::class, 'estudiante_id', 'idEstudiante');
    }

    public function curso()
    {
        return $this->belongsTo(Curso::class, 'curso_id', 'idCurso');
    }

    // Generar número de certificado único
    public static function generarNumeroCertificado($estudiante_id, $curso_id)
    {
        return 'CERT-' . date('Y') . '-' . str_pad($estudiante_id, 6, '0', STR_PAD_LEFT) . '-' . str_pad($curso_id, 6, '0', STR_PAD_LEFT);
    }

    // Incrementar contador de descargas
    public function registrarDescarga()
    {
        $this->increment('descargas');
        $this->update(['ultima_descarga' => now()]);
    }
}
