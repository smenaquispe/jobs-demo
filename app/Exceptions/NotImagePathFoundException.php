<?php

namespace App\Exceptions;

use Exception;

class NotImagePathFoundException extends Exception
{
    public static function create(string $path)
    {
        return new static("No se encontró la imagen en la ruta {$path}");
    }
}
