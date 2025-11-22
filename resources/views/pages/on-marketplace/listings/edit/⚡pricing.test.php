<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

it('requires price', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'price' => 100.00,
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', '')
        ->call('update')
        ->assertHasErrors(['price' => 'required']);
});

it('requires price to be numeric', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'price' => 100.00,
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', 'notanumber')
        ->call('update')
        ->assertHasErrors(['price' => 'numeric']);
});

it('requires price to be >= 0', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'price' => 100.00,
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', -5)
        ->call('update')
        ->assertHasErrors(['price' => 'min']);
});

it('edits a listing price and updates the record', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'price' => 100.00,
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', 250.50)
        ->call('update')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->price_dollars)->toBe(250.50);
    expect($listing->price)->toBe(25050);
});

it('redirects to availability when listing is draft after pricing update', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'price' => 100.00,
        'status' => 'draft',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('price', 200.00)
        ->call('update')
        ->assertRedirect(route('on-marketplace.listings.edit.availability', [$marketplace, $listing]));
});

it('forbids non-owners from updating pricing', function () {
    $marketplace = Marketplace::factory()->create();
    $owner = User::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($owner)->create([
        'price' => 100.00,
    ]);
    $otherUser = User::factory()->create();

    Livewire::actingAs($otherUser)
        ->test('pages::on-marketplace.listings.edit.pricing', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->assertForbidden();
});
