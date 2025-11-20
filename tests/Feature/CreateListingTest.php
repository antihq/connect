<?php

use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('creates a new listing with draft status, stores creator, and requires title and description', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    // Validation: both fields required
    Livewire::actingAs($user)
        ->test('on-marketplace.listings.create', ['marketplace' => $marketplace])
        ->set('title', '')
        ->set('description', '')
        ->call('create')
        ->assertHasErrors(['title' => 'required', 'description' => 'required']);

    // Success: valid data
    Livewire::actingAs($user)
        ->test('on-marketplace.listings.create', ['marketplace' => $marketplace])
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
