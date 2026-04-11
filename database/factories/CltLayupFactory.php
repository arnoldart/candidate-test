<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Supplier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CltLayup>
 */
class CltLayupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => Supplier::factory(),
            'name' => 'Standard ' . fake()->randomElement(['3', '5', '7']) . '-Ply ' . fake()->word() . ' Panel',
            'species_grade' => fake()->randomElement(['Spruce/Pine/Fir', 'Douglas Fir C', 'Radiata Pine V2', 'Mixed SPF V1']),
            'revision' => fake()->numberBetween(1, 4),
            'status' => fake()->randomElement(['Active', 'Draft']),
            'created_by' => fake()->name(),
        ];
    }
}
