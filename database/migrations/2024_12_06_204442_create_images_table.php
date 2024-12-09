<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('images', function (Blueprint $table) {
            $table->id(); // Campo de ID autoincremental
            $table->string('path'); // Ruta del archivo original
            $table->string('status'); // Estado de la conversión (pendiente, convertido, etc.)
            $table->string('output_format'); // Formato de salida deseado (ej. jpeg, png, etc.)
            $table->string('converted_path')->nullable(); // Ruta del archivo convertido (puede ser nulo)
            $table->timestamps(); // Campos de fecha de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
