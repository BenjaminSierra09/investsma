<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agent>
 */
class AgentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'title' => fake()->randomElement(['Asesor inmobiliario', 'Broker asociada', 'Especialista en inversión']),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '+52 415 '.fake()->numerify('### ####'),
            'whatsapp' => '52'.fake()->numerify('415#######'),
            'photo_url' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80',
            'bio' => fake()->paragraph(3),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (): array => [
            'is_active' => false,
        ]);
    }
}
