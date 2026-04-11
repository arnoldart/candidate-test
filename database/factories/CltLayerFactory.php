<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\CltLayup;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CltLayer>
 */
class CltLayerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'layup_id' => CltLayup::factory(),
            'layer_order' => 1,
            'thickness' => fake()->randomElement([20.0, 30.0, 40.0, 45.0]),
            'width' => fake()->randomElement([1500.0, 2000.0, 3000.0]),
            'angle' => fake()->randomElement([0.0, 90.0]),
        ];
    }
}
