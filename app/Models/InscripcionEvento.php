<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscripcionEvento extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscripcion_eventos';

    protected $primaryKey = 'idInscripcion';

    protected $fillable = [
        'idEvento',
        'user_id',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'documento',
        'estado',
        'pago_realizado',
    ];

    protected $casts = [
        'estado' => 'string',
        'pago_realizado' => 'boolean',
    ];

    public function evento()
    {
        return $this->belongsTo(Evento::class, 'idEvento', 'idEvento');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Scopes
    public function scopeConfirmadas($query)
    {
        return $query->where('estado', 'confirmada');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('estado', 'cancelada');
    }

    public function scopePorEvento($query, $idEvento)
    {
        return $query->where('idEvento', $idEvento);
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
