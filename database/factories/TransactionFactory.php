<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = $this->faker->dateTimeBetween('-1 month', 'now');
        $duration = $this->faker->numberBetween(1, 14);
        $price = $this->faker->randomFloat(2, 50, 500);
        $priceCents = (int) round($price * 100);

        return [
            'marketplace_id' => \App\Models\Marketplace::factory(),
            'listing_id' => \App\Models\Listing::factory(),
            'user_id' => \App\Models\User::factory(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify("+{$duration} days"),
            'duration' => $duration,
            'price_per_unit' => $priceCents,
            'total' => $priceCents * $duration,
            'status' => 'pending',
        ];
    }
}
