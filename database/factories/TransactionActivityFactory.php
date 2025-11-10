<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TransactionActivity>
 */
class TransactionActivityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'transaction_id' => \App\Models\Transaction::factory(),
            'type' => $this->faker->randomElement(['created', 'payment_initiated', 'payment_succeeded', 'payment_failed']),
            'description' => $this->faker->sentence(),
            'meta' => ['ip' => $this->faker->ipv4],
        ];
    }
}
