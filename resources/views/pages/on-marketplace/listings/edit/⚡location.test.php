<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

it('redirects to pricing when listing is draft after location update', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create();
    $marketplace->addMember($user);
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'address' => '123 Main St',
        'apt_suite' => 'Apt 1',
        'status' => 'draft',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('address', '456 Oak Ave')
        ->set('apt_suite', 'Suite 200')
        ->call('update')
        ->assertRedirect(route('on-marketplace.listings.edit.pricing', [$marketplace, $listing]));
});

it('updates address and apt_suite for a listing', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create();
    $marketplace->addMember($user);
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'address' => '123 Main St',
        'apt_suite' => 'Apt 1',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('address', '456 Oak Ave')
        ->set('apt_suite', 'Suite 200')
        ->call('update')
        ->assertHasNoErrors();

    $listing = $listing->fresh();
    expect($listing->address)->toBe('456 Oak Ave');
    expect($listing->apt_suite)->toBe('Suite 200');
});

it('updates address and clears apt_suite for a listing', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create();
    $marketplace->addMember($user);
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'address' => '456 Oak Ave',
        'apt_suite' => 'Suite 200',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('address', '789 Pine Rd')
        ->set('apt_suite', '')
        ->call('update')
        ->assertHasNoErrors();

    $listing = $listing->fresh();
    expect($listing->address)->toBe('789 Pine Rd');
    expect($listing->apt_suite)->toBe('');
});

it('requires address when updating a listing location', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create();
    $marketplace->addMember($user);
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'address' => '123 Main St',
        'apt_suite' => 'Apt 1',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.location', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('address', '')
        ->set('apt_suite', '')
        ->call('update')
        ->assertHasErrors(['address' => 'required']);
});
