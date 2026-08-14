<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class Utf8Sanitizer
{
    /**
     * Sanitiza recursivamente cualquier estructura de datos (string, array, Collection,
     * Eloquent Model, stdClass) eliminando bytes UTF-8 inválidos y caracteres de control.
     *
     * @param  mixed  $valor
     * @return mixed
     */
    public function sanitizarValorRecursivo($valor)
    {
        if (is_string($valor)) {
            return $this->sanitizarTextoUtf8($valor);
        }

        if (is_array($valor)) {
            return array_map(fn ($item) => $this->sanitizarValorRecursivo($item), $valor);
        }

        if ($valor instanceof Collection) {
            return $valor->map(fn ($item) => $this->sanitizarValorRecursivo($item));
        }

        if ($valor instanceof Model) {
            foreach ($valor->getAttributes() as $campo => $v) {
                if (is_string($v)) {
                    $valor->setAttribute($campo, $this->sanitizarTextoUtf8($v));
                }
            }

            foreach ($valor->getRelations() as $nombreRelacion => $relacionado) {
                $valor->setRelation($nombreRelacion, $this->sanitizarValorRecursivo($relacionado));
            }

            return $valor;
        }

        if ($valor instanceof \stdClass) {
            foreach ($valor as $prop => $v) {
                if (is_string($v)) {
                    $valor->{$prop} = $this->sanitizarTextoUtf8($v);
                } elseif (is_array($v) || is_object($v)) {
                    $valor->{$prop} = $this->sanitizarValorRecursivo($v);
                }
            }

            return $valor;
        }

        return $valor;
    }

    /**
     * Sanitiza recursivamente todos los valores string dentro de un array (incluyendo sub-arrays).
     */
    public function sanitizarArrayUtf8(array $datos): array
    {
        return array_map(function ($item) {
            if (is_string($item)) {
                return $this->sanitizarTextoUtf8($item);
            }

            if (is_array($item)) {
                return $this->sanitizarArrayUtf8($item);
            }

            return $item;
        }, $datos);
    }

    /**
     * Normaliza una colección de modelos sanitizando cada modelo.
     */
    public function normalizarColeccionModelos($coleccion)
    {
        if (! $coleccion instanceof Collection) {
            return $coleccion;
        }

        $coleccion->each(function ($modelo) {
            $this->normalizarModeloUtf8($modelo);
        });

        return $coleccion;
    }

    /**
     * Sanitiza los atributos y relaciones de un modelo Eloquent.
     */
    public function normalizarModeloUtf8($modelo): void
    {
        if (! is_object($modelo)) {
            return;
        }

        foreach ($modelo->getAttributes() as $campo => $valor) {
            if (is_string($valor)) {
                $modelo->setAttribute($campo, $this->sanitizarTextoUtf8($valor));
            }
        }

        foreach ($modelo->getRelations() as $relacion => $valor) {
            if ($valor instanceof Collection) {
                $valor->each(function ($item) {
                    $this->normalizarModeloUtf8($item);
                });

                continue;
            }

            if ($valor instanceof Model) {
                $this->normalizarModeloUtf8($valor);
            }
        }
    }

    /**
     * Elimina secuencias de bytes UTF-8 inválidas y caracteres de control.
     */
    public function sanitizarTextoUtf8(string $valor): string
    {
        // Paso 1: Eliminar secuencias de bytes UTF-8 inválidas usando mb_convert_encoding.
        // PHP 8.0+ reemplaza caracteres inválidos en lugar de retornar false.
        $texto = @mb_convert_encoding($valor, 'UTF-8', 'UTF-8');

        if ($texto === false) {
            // Fallback con iconv en caso extremo
            $texto = @iconv('UTF-8', 'UTF-8//IGNORE', $valor) ?: '';
        }

        if ($texto === '') {
            return '';
        }

        // Paso 2: Eliminar caracteres de control (excepto tab, newline, carriage return)
        // Se usa un patrón sin modificador /u para evitar errores de PCRE en edge cases.
        $resultado = @preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $texto);

        return is_string($resultado) ? $resultado : $texto;
    }
}
