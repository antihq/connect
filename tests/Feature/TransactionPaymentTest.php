<?php

use App\Models\User;
use App\Models\Listing;
use App\Models\Transaction;
use Livewire\Volt\Volt;
use function Pest\Laravel\assertDatabaseHas;

it('only allows the transaction owner to access the payment page', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();
    $listing = Listing::factory()->create();
    $marketplace = $listing->marketplace ?? \App\Models\Marketplace::factory()->create();
    $listing->marketplace()->associate($marketplace)->save();
    $transaction = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'pending',
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'nights' => 3,
        'price_per_night' => 100,
        'total' => 300,
    ]);


    Volt::actingAs($user)
        ->test('marketplaces.transactions.pay', ['marketplace' => $marketplace, 'transaction' => $transaction])
        ->assertSee($listing->title)
        ->assertSee('Pay Now');

    Volt::actingAs($other)
        ->test('marketplaces.transactions.pay', ['marketplace' => $marketplace, 'transaction' => $transaction])
        ->assertStatus(403);
});

it('marks the transaction as paid and redirects to confirmation', function () {
    $user = User::factory()->create();
    $listing = Listing::factory()->create();
    $marketplace = $listing->marketplace ?? \App\Models\Marketplace::factory()->create();
    $listing->marketplace()->associate($marketplace)->save();
    $transaction = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'pending',
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'nights' => 3,
        'price_per_night' => 100,
        'total' => 300,
    ]);


    Volt::actingAs($user)
        ->test('marketplaces.transactions.pay', ['marketplace' => $marketplace, 'transaction' => $transaction])
        ->call('pay')
        ->assertRedirect(route('marketplaces.transactions.pay.confirmation', ['marketplace' => $marketplace->id, 'transaction' => $transaction->id]));

    assertDatabaseHas('transactions', [
        'id' => $transaction->id,
        'status' => 'paid',
    ]);
});

it('shows the confirmation page with correct details', function () {
    $user = User::factory()->create();
    $listing = Listing::factory()->create();
    $marketplace = \App\Models\Marketplace::factory()->create();
    $listing->marketplace()->associate($marketplace)->save();
    $transaction = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'pending',
        'start_date' => now()->addDays(2)->toDateString(),
        'end_date' => now()->addDays(5)->toDateString(),
        'nights' => 3,
        'price_per_night' => 100,
        'total' => 300,
    ]);

    $marketplace = $listing->marketplace ?? \App\Models\Marketplace::factory()->create();
    $listing->marketplace()->associate($marketplace)->save();

    Volt::actingAs($user)
        ->test('marketplaces.transactions.pay-confirmation', ['marketplace' => $marketplace, 'transaction' => $transaction])
        ->assertSee('Payment Successful')
        ->assertSee($listing->title)
        ->assertSee('300');
});
