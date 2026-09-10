<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuracion extends Model
{
    use HasFactory;

    protected $table = 'configuraciones';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'grupo',
        'descripcion',
    ];

    /**
     * Obtiene el valor de una configuración por su clave.
     */
    public static function obtener(string $clave, mixed $default = null): mixed
    {
        $configuracion = static::query()->where('clave', $clave)->first();

        if (! $configuracion || $configuracion->valor === null || $configuracion->valor === '') {
            return $default;
        }

        return $configuracion->valor;
    }

    /**
     * Crea o actualiza el valor de una configuración.
     */
    public static function establecer(string $clave, mixed $valor, string $tipo = 'texto', string $grupo = 'general'): void
    {
        static::query()->updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'tipo' => $tipo,
                'grupo' => $grupo,
            ]
        );
    }

    /**
     * Devuelve todas las configuraciones como un arreglo clave => valor.
     *
     * @return array<string, string|null>
     */
    public static function mapa(): array
    {
        return static::query()
            ->pluck('valor', 'clave')
            ->toArray();
    }
}
