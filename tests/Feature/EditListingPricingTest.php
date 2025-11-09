<?php

use App\Models\Marketplace;
use App\Models\Listing;
use Livewire\Volt\Volt;
use App\Models\User;

use function Pest\Laravel\assertDatabaseHas;

it('edits a listing price, requires price, must be numeric, and updates the record', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'price' => 100.00,
    ]);

    // Validation: price required
    Volt::actingAs($user)
        ->test('marketplaces.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', '')
        ->call('update')
        ->assertHasErrors(['price' => 'required']);

    // Validation: price must be numeric
    Volt::actingAs($user)
        ->test('marketplaces.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', 'notanumber')
        ->call('update')
        ->assertHasErrors(['price' => 'numeric']);

    // Validation: price must be >= 0
    Volt::actingAs($user)
        ->test('marketplaces.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', -5)
        ->call('update')
        ->assertHasErrors(['price' => 'min']);

    // Success: valid data
    Volt::actingAs($user)
        ->test('marketplaces.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', 250.50)
        ->call('update')
        ->assertHasNoErrors();

    assertDatabaseHas('listings', [
        'id' => $listing->id,
        'price' => 250.50,
    ]);
});
