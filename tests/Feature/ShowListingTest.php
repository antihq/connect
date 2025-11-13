<?php

use App\Models\Marketplace;
use App\Models\Listing;
use App\Models\User;
use App\Models\Transaction;
use Livewire\Volt\Volt;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;

it('allows an authenticated user to book available dates', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create(['price' => 100]);

    $start = now()->addDays(2)->toDateString();
    $end = now()->addDays(5)->toDateString();

    Volt::actingAs($user)
        ->test('marketplaces.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => $start, 'end' => $end])
        ->call('requestToBook')
        ->assertRedirect(route('marketplaces.transactions.pay', [
            'marketplace' => $marketplace->id,
            'transaction' => Transaction::where('listing_id', $listing->id)->where('user_id', $user->id)->latest()->first()->id,
        ]));

    $transaction = Transaction::where('listing_id', $listing->id)->where('user_id', $user->id)->latest()->first();
    assertDatabaseHas('transactions', [
        'listing_id' => $listing->id,
        'user_id' => $user->id,
        'start_date' => $start . ' 00:00:00',
        'end_date' => $end . ' 00:00:00',
        'nights' => 3,
        'price_per_night' => 100,
        'total' => 300,
        'status' => 'pending',
    ]);
    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $transaction->id,
        'type' => 'created',
    ]);
});

it('prevents booking if not logged in', function () {
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create(['price' => 100]);
    $start = now()->addDays(2)->toDateString();
    $end = now()->addDays(5)->toDateString();

Volt::test('marketplaces.listings.show', [
    'marketplace' => $marketplace,
    'listing' => $listing,
])
    ->set('range', ['start' => $start, 'end' => $end])
    ->call('requestToBook')
        ->assertSet('bookingError', 'You must be logged in to book.');

    assertDatabaseMissing('transactions', [
        'listing_id' => $listing->id,
        'start_date' => $start,
        'end_date' => $end,
    ]);
});

it('prevents booking overlapping dates', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create(['price' => 100]);
    $existing = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'nights' => 3,
        'price_per_night' => 100,
        'total' => 300,
        'status' => 'pending',
    ]);

    $overlapStart = now()->addDays(4)->toDateString();
    $overlapEnd = now()->addDays(7)->toDateString();

Volt::actingAs($user)
        ->test('marketplaces.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => $overlapStart, 'end' => $overlapEnd])
        ->call('requestToBook')
        ->assertSet('bookingError', 'Selected dates are not available.');

    assertDatabaseMissing('transactions', [
        'listing_id' => $listing->id,
        'start_date' => $overlapStart,
        'end_date' => $overlapEnd,
    ]);
});

it('prevents booking with invalid dates', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create(['price' => 100]);

    // End before start
    $start = now()->addDays(5)->toDateString();
    $end = now()->addDays(2)->toDateString();
    Volt::actingAs($user)
        ->test('marketplaces.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => $start, 'end' => $end])
        ->call('requestToBook')
        ->assertSet('bookingError', 'End date must be after start date.');

    // Missing dates
Volt::actingAs($user)
        ->test('marketplaces.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => null, 'end' => null])
        ->call('requestToBook')
        ->assertSet('bookingError', 'Please select both start and end dates.');
});
