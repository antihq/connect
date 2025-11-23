<?php

namespace Database\Factories;

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PayoutSetting>
 */
class PayoutSettingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'marketplace_id' => Marketplace::factory(),
            'account_type' => 'individual',
            'country' => 'US',
            'stripe_account_id' => 'acct_test123',
            'onboarding_status' => null,
        ];
    }
}
