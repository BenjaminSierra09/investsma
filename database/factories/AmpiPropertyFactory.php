<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AmpiProperty>
 */
class AmpiPropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->numberBetween(150000, 1500000);
        $currency = fake()->randomElement(['USD', 'MXN', 'CAD', 'EUR']);
        $name = fake()->words(3, true);

        return [
            'mls_id' => 'SMA-'.fake()->unique()->numberBetween(1000, 9999),
            'external_id' => fake()->unique()->numberBetween(10000, 99999),
            'name' => $name,
            'slug' => str($name)->slug()->toString(),
            'status' => 'For Sale',
            'category' => fake()->randomElement(['Residential', 'Land and Lots', 'Commercial']),
            'office_id' => 32,
            'city' => 'San Miguel de Allende',
            'neighborhood' => fake()->randomElement(['Centro', 'Atascadero', 'Los Frailes']),
            'price' => $price,
            'currency' => $currency,
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->randomFloat(1, 1, 5),
            'floors' => fake()->numberBetween(1, 3),
            'construction_meters' => fake()->numberBetween(80, 600),
            'lot_meters' => fake()->numberBetween(90, 1000),
            'pool' => fake()->boolean(),
            'casita' => fake()->boolean(),
            'latitude' => fake()->randomFloat(7, 20.88, 20.95),
            'longitude' => fake()->randomFloat(7, -100.78, -100.70),
            'featured_image' => fake()->imageUrl(),
            'photos' => [],
            'raw_payload' => [
                'mls_id' => null,
                'name' => $name,
                'price' => $price,
                'currency' => $currency,
            ],
            'last_synced_at' => now(),
            'is_active' => true,
        ];
    }
}
