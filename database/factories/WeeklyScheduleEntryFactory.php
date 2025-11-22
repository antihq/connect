<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WeeklyScheduleEntry>
 */
class WeeklyScheduleEntryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'listing_id' => \App\Models\Listing::factory(),
            'day' => $this->faker->randomElement(['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']),
            'available' => $this->faker->boolean,
            'start_time' => $this->faker->optional()->time('H:i'),
            'end_time' => $this->faker->optional()->time('H:i'),
        ];
    }
}
