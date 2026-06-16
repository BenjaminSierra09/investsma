<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->currencyCode(),
            'code' => fake()->unique()->currencyCode(),
            'symbol' => '$',
            'exchange_rate' => fake()->randomFloat(6, 1, 25),
            'is_base' => false,
        ];
    }

    public function mxn(): static
    {
        return $this->state(fn (): array => [
            'name' => 'Mexican Peso',
            'code' => 'MXN',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_base' => true,
        ]);
    }

    public function usd(): static
    {
        return $this->state(fn (): array => [
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => 'US$',
            'exchange_rate' => 17,
            'is_base' => false,
        ]);
    }
}
