<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Livewire\Livewire;

it('edits a listing and updates the record', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'title' => 'Old Title',
        'description' => 'Old description',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.details', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('title', 'Updated Title')
        ->set('description', 'Updated description')
        ->call('update')
        ->assertHasNoErrors();

    $listing->refresh();
    expect($listing->title)->toBe('Updated Title');
    expect($listing->description)->toBe('Updated description');
});

it('requires title and description for update', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'title' => 'Old Title',
        'description' => 'Old description',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.details', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('title', '')
        ->set('description', '')
        ->call('update')
        ->assertHasErrors(['title' => 'required', 'description' => 'required']);
});

it('redirects to location step if listing is draft on update', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($user)->create([
        'title' => 'Draft Title',
        'description' => 'Draft description',
        'status' => 'draft',
    ]);

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.edit.details', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('title', 'Updated Draft Title')
        ->set('description', 'Updated draft description')
        ->call('update')
        ->assertRedirect(route('on-marketplace.listings.edit.location', [$marketplace, $listing]));
});
