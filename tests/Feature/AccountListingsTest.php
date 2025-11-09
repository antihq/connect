<?php

use App\Models\User;
use App\Models\Marketplace;
use App\Models\Listing;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('can open a listing to the public', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'status' => 'draft',
    ]);

    $this->actingAs($user);

    Volt::test('marketplaces.account.listings', [
        'marketplace' => $marketplace,
    ])->call('openToPublic', $listing->id)
      ->assertOk();

    $listing->refresh();
    expect($listing->status)->toBe('public');
});

it('can close a listing to the public', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'status' => 'public',
    ]);

    $this->actingAs($user);

    Volt::test('marketplaces.account.listings', [
        'marketplace' => $marketplace,
    ])->call('closeToPublic', $listing->id)
      ->assertOk();

    $listing->refresh();
    expect($listing->status)->toBe('draft');
});
