<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\assertDatabaseHas;

it('edits a listing address and apt_suite, requires address, and updates the record', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'address' => '123 Main St',
        'apt_suite' => 'Apt 1',
    ]);

    // Validation: address required
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('address', '')
        ->set('apt_suite', '')
        ->call('update')
        ->assertHasErrors(['address' => 'required']);

    // Success: valid data (with apt_suite)
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('address', '456 Oak Ave')
        ->set('apt_suite', 'Suite 200')
        ->call('update')
        ->assertHasNoErrors();

    assertDatabaseHas('listings', [
        'id' => $listing->id,
        'address' => '456 Oak Ave',
        'apt_suite' => 'Suite 200',
    ]);

    // Success: valid data (no apt_suite)
    Volt::actingAs($user)
        ->test('on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing->fresh(),
        ])
        ->set('address', '789 Pine Rd')
        ->set('apt_suite', '')
        ->call('update')
        ->assertHasNoErrors();

    assertDatabaseHas('listings', [
        'id' => $listing->id,
        'address' => '789 Pine Rd',
        'apt_suite' => '',
    ]);
});
