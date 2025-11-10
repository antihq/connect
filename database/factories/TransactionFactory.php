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
        $nights = $this->faker->numberBetween(1, 14);
        $price = $this->faker->randomFloat(2, 50, 500);
        return [
            'marketplace_id' => \App\Models\Marketplace::factory(),
            'listing_id' => \App\Models\Listing::factory(),
            'user_id' => \App\Models\User::factory(),
            'start_date' => $start,
            'end_date' => (clone $start)->modify("+{$nights} days"),
            'nights' => $nights,
            'price_per_night' => $price,
            'total' => $price * $nights,
            'status' => 'pending',
        ];
    }
}
