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
        ->set('price', 25050)
        ->call('update')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->price_dollars)->toBe(250.50);
    expect($listing->price)->toBe(25050);
});
