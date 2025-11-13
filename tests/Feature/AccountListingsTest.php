<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
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
        'title' => 'Test',
        'description' => 'Test desc',
        'address' => '123 Main St',
        'price' => 100,
        'weekly_schedule' => ['monday' => ['09:00-17:00']],
        'photos' => ['photo1.jpg'],
    ]);

    $this->actingAs($user);

    Volt::test('marketplaces.account.listings', [
        'marketplace' => $marketplace,
    ])->call('openToPublic', $listing->id)
        ->assertOk();

    $listing->refresh();
    expect($listing->status)->toBe('public');
});

it('cannot open a listing to the public if required fields are missing', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'status' => 'draft',
        'title' => '', // missing title
        'description' => '', // missing description
        'address' => '', // missing address
        'price' => null, // missing price
        'weekly_schedule' => [], // missing schedule
        'photos' => [], // missing photos
    ]);

    $this->actingAs($user);

    Volt::test('marketplaces.account.listings', [
        'marketplace' => $marketplace,
    ])->call('openToPublic', $listing->id)
        ->assertHasErrors(['openToPublic']);

    $listing->refresh();
    expect($listing->status)->not()->toBe('public');
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
