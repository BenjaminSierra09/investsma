<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->unique()->sentence(3);
        $gallery = [
            'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1494526585095-c41746248156?auto=format&fit=crop&w=1600&q=80',
        ];

        return [
            'agent_id' => null,
            'title' => $title,
            'slug' => Str::slug($title).'-'.fake()->unique()->numerify('##'),
            'status' => 'published',
            'listing_type' => fake()->randomElement(['sale', 'rent']),
            'featured' => false,
            'currency' => 'USD',
            'price' => fake()->numberBetween(180000, 1200000),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 5),
            'construction_m2' => fake()->numberBetween(90, 420),
            'lot_m2' => fake()->numberBetween(120, 800),
            'location' => fake()->randomElement(['Centro', 'Guadiana', 'Atascadero', 'Los Frailes']).', San Miguel de Allende',
            'cover_image' => $gallery[0],
            'gallery' => $gallery,
            'summary' => fake()->sentence(18),
            'description' => fake()->paragraphs(4, true),
            'contact_email' => 'info@investsma.com',
            'contact_phone' => '+52 415 125 5042',
            'contact_whatsapp' => '524151255042',
            'meta_title' => $title.' | investsma',
            'meta_description' => fake()->sentence(18),
            'published_at' => now(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (): array => [
            'status' => 'draft',
            'published_at' => null,
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (): array => [
            'featured' => true,
        ]);
    }

    public function forSale(): static
    {
        return $this->state(fn (): array => [
            'listing_type' => 'sale',
        ]);
    }

    public function forRent(): static
    {
        return $this->state(fn (): array => [
            'listing_type' => 'rent',
        ]);
    }
}
