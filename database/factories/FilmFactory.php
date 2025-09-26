<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Film>
 */
class FilmFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $ratings = ['G', 'PG', 'PG-13', 'R', 'NC-17'];
        $specialFeatures = ['Trailers', 'Commentaries', 'Deleted Scenes', 'Behind the Scenes'];
        
        // Seleccionar características especiales aleatorias (0-4 características)
        $selectedFeatures = $this->faker->randomElements($specialFeatures, $this->faker->numberBetween(0, 4));
        
        return [
            'title' => $this->faker->sentence(3, false), // Título de 3 palabras
            'description' => $this->faker->paragraph(3),
            'release_year' => $this->faker->numberBetween(1980, date('Y')),
            'language_id' => 1, // Asumimos English como idioma principal por defecto
            'original_language_id' => $this->faker->optional(0.3)->randomElement([1, 2, 3]), // 30% probabilidad de tener idioma original
            'rental_duration' => $this->faker->numberBetween(3, 7),
            'rental_rate' => $this->faker->randomFloat(2, 0.99, 6.99),
            'length' => $this->faker->numberBetween(80, 200),
            'replacement_cost' => $this->faker->randomFloat(2, 9.99, 29.99),
            'rating' => $this->faker->randomElement($ratings),
            'special_features' => count($selectedFeatures) > 0 ? implode(',', $selectedFeatures) : null,
        ];
    }
}
