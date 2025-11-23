<?php

namespace Database\Factories;

use App\Models\Marketplace;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Listing>
 */
class ListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'marketplace_id' => Marketplace::factory(),
            'user_id' => \App\Models\User::factory(),
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'price' => $this->faker->numberBetween(1000, 10000), // price in cents
            'status' => 'draft',
        ];
    }

    /**
     * Indicate that the listing is public.
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'public',
        ]);
    }
}
