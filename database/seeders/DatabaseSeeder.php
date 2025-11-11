<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $user = User::factory()->withPersonalOrganizationAndSubscription()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Create a marketplace for the user's first organization
        $organization = $user->organizations()->first();
        if ($organization) {
            $organization->marketplace()->create([
                'name' => 'Test Marketplace',
                'slug' => 'test-marketplace',
            ]);
        }
    }
}
