<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

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
    ]);

    // Add a photo
    $listing->photos()->create(['path' => 'photos/photo1.jpg']);

    // Add weekly schedule entry
    \App\Models\WeeklyScheduleEntry::factory()->create([
        'listing_id' => $listing->id,
        'day' => 'monday',
        'available' => true,
        'start_time' => '09:00',
        'end_time' => '17:00',
    ]);

    $this->actingAs($user);

    Livewire::test('pages::marketplaces.account.listings', [
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
    ]);
    // No photos created (simulates missing photos)
    // No schedule entries created (simulates missing schedule)

    $this->actingAs($user);

    Livewire::test('pages::marketplaces.account.listings', [
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

    Livewire::test('pages::marketplaces.account.listings', [
        'marketplace' => $marketplace,
    ])->call('closeToPublic', $listing->id)
        ->assertOk();

    $listing->refresh();
    expect($listing->status)->toBe('draft');
});
