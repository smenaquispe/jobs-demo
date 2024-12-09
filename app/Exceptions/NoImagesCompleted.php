<?php

namespace App\Exceptions;

use Exception;

class NoImagesCompleted extends Exception
{
    public function create(int $imagesInDatabase, int $imagesToBeConverted)
    {
        return new static("No se han completado todas las imágenes en la base de datos ({$imagesInDatabase}) de las que se esperaban ({$imagesToBeConverted})");
    }
}
