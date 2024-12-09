<?php

namespace Database\Factories;

use App\Models\Image;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Image>
 */
class ImageFactory extends Factory
{
    protected $model = Image::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'path' => $this->faker->imageUrl(), // Genera una URL de imagen falsa
            'status' => $this->faker->randomElement(['pendiente', 'convertido', 'fallido']), // Estado aleatorio
            'output_format' => $this->faker->randomElement(['jpeg', 'png', 'gif']), // Formato aleatorio
            'converted_path' => "", // Genera una URL falsa para la ruta de la imagen convertida
        ];
    }
}
