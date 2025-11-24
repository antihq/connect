<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Livewire;

it('allows an authenticated user to book available dates', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->public()->for($marketplace)->create(['price' => 10000]);

    $start = now()->addDays(2)->toDateString();
    $end = now()->addDays(5)->toDateString();

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.show', [
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

    expect($transaction)->not->toBeNull();
    expect($transaction->start_date->toDateString())->toBe($start);
    expect($transaction->end_date->toDateString())->toBe($end);
    expect($transaction->duration)->toBe(3);
    expect($transaction->price_per_unit)->toEqual(10000);
    expect($transaction->total)->toEqual(30000);
    expect($transaction->status)->toBe('pending');

    $activity = $transaction->activities()->where('type', 'created')->where('user_id', $user->id)->first();
    expect($activity)->not->toBeNull();
    expect($activity->transaction_id)->toBe($transaction->id);
    expect($activity->type)->toBe('created');
    expect($activity->user_id)->toBe($user->id);
});

it('prevents booking if not logged in', function () {
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->public()->for($marketplace)->create(['price' => 10000]);
    $start = now()->addDays(2)->toDateString();
    $end = now()->addDays(5)->toDateString();

    Livewire::test('pages::on-marketplace.listings.show', [
        'marketplace' => $marketplace,
        'listing' => $listing,
    ])
        ->set('range', ['start' => $start, 'end' => $end])
        ->call('requestToBook')
        ->assertRedirect(route('on-marketplace.sign-in', ['marketplace' => $marketplace->slug]));

    $transaction = Transaction::where('listing_id', $listing->id)
        ->where('start_date', $start)
        ->where('end_date', $end)
        ->first();
    expect($transaction)->toBeNull();
});

it('prevents booking overlapping dates', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->public()->for($marketplace)->create(['price' => 10000]);
    $existing = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'duration' => 3,
        'price_per_unit' => 10000,
        'total' => 30000,
        'status' => 'pending',
    ]);

    $overlapStart = now()->addDays(4)->toDateString();
    $overlapEnd = now()->addDays(7)->toDateString();

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => $overlapStart, 'end' => $overlapEnd])
        ->call('requestToBook')
        ->assertSet('bookingError', 'Selected dates are not available.');

    $transaction = Transaction::where('listing_id', $listing->id)
        ->where('start_date', $overlapStart)
        ->where('end_date', $overlapEnd)
        ->first();
    expect($transaction)->toBeNull();
});

it('returns 404 for non-public listing status', function () {
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create(['status' => 'draft']);
    $response = Livewire::test('pages::on-marketplace.listings.show', [
        'marketplace' => $marketplace,
        'listing' => $listing,
    ]);
    $response->assertStatus(404);
});

it('prevents booking with invalid dates', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->public()->for($marketplace)->create(['price' => 10000]);

    $start = now()->addDays(5)->toDateString();
    $end = now()->addDays(2)->toDateString();
    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => $start, 'end' => $end])
        ->call('requestToBook')
        ->assertSet('bookingError', 'End date must be after start date.');

    Livewire::actingAs($user)
        ->test('pages::on-marketplace.listings.show', [
            'marketplace' => $marketplace,
            'listing' => $listing,
        ])
        ->set('range', ['start' => null, 'end' => null])
        ->call('requestToBook')
        ->assertSet('bookingError', 'Please select both start and end dates.');
});
