<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Supplier>
 */
class SupplierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $createdAt = fake()->dateTimeBetween('-1 year', 'now');
        
        return [
            'name' => fake()->company() . ' Timber Co.',
            'primary_contact' => fake()->unique()->safeEmail(),
            'location' => fake()->city() . ', ' . fake()->countryCode(),
            'material_certifications' => fake()->randomElement(['FSC Certified', 'PEFC Certified', 'FSC & PEFC', 'ISO 9001']),
            'last_audit_date' => fake()->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'created_at' => $createdAt,
            'updated_at' => fake()->dateTimeBetween($createdAt, 'now'),
        ];
    }
}
