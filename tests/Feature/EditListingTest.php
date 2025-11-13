<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\assertDatabaseHas;

it('edits a listing, requires title and description, and updates the record', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'title' => 'Old Title',
        'description' => 'Old description',
    ]);

    // Validation: both fields required
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.details', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('title', '')
        ->set('description', '')
        ->call('update')
        ->assertHasErrors(['title' => 'required', 'description' => 'required']);

    // Success: valid data
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.details', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated description')
        ->call('update')
        ->assertHasNoErrors();

    assertDatabaseHas('listings', [
        'id' => $listing->id,
        'title' => 'Updated Title',
        'description' => 'Updated description',
    ]);
});
