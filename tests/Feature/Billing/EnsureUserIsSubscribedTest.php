<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('redirects unsubscribed users to the subscription-required page when accessing protected routes', function () {
    $user = User::factory()->withPersonalOrganization()->create([
        'email_verified_at' => now(),
    ]);

    expect($user->currentOrganization->subscribed())->toBeFalse();

    actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('subscription-required'));
});
