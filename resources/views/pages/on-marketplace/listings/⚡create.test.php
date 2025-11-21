<?php

use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

it('requires the user to be a member of the marketplace to create a listing', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    // Not a member: should fail (403) on mount
    actingAs($user)
        ->get(route('on-marketplace.listings.create', $marketplace))
        ->assertForbidden();

    // Add user as member
    $marketplace->addMember($user);

    // Validation: both fields required
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.create', ['marketplace' => $marketplace])
        ->set('title', '')
        ->set('description', '')
        ->call('create')
        ->assertHasErrors(['title' => 'required', 'description' => 'required']);

    // Success: valid data
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.create', ['marketplace' => $marketplace])
        ->set('title', 'Test Listing')
        ->set('description', 'Test description')
        ->call('create')
        ->assertHasNoErrors();

    assertDatabaseHas('listings', [
        'title' => 'Test Listing',
        'description' => 'Test description',
        'status' => 'draft',
        'marketplace_id' => $marketplace->id,
        'user_id' => $user->id,
    ]);
});
