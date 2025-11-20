<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'solicitudes';
    protected $primaryKey = 'idSolicitud';

    protected $fillable = [
        'tipo',
        'asunto',
        'mensaje',
        'telefono',
        'email_contacto',
        'datos_adicionales',
        'estado',
        'respuesta',
        'fecha_respuesta',
        'user_id',
        'atendido_por_admin'
    ];

    protected $casts = [
        'datos_adicionales' => 'array',
        'fecha_respuesta' => 'datetime'
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function administrador()
    {
        return $this->belongsTo(Administrador::class, 'atendido_por_admin', 'idAdmin');
    }

    // Scopes
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeResueltas($query)
    {
        return $query->where('estado', 'resuelta');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    // Helpers
    public function marcarComoResuelta($respuesta = null, $adminId = null)
    {
        $this->update([
            'estado' => 'resuelta',
            'respuesta' => $respuesta,
            'fecha_respuesta' => now(),
            'atendido_por_admin' => $adminId
        ]);
    }

    public function getEsPendienteAttribute()
    {
        return $this->estado === 'pendiente';
    }
}
