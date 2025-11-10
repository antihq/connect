<?php

use App\Models\User;
use App\Models\Marketplace;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Livewire\Volt\Volt;
use function Pest\Laravel\assertDatabaseHas;

it('shows only the user\'s sales in the inbox', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $sale = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);
    $otherListing = Listing::factory()->for($marketplace)->create();
    $notMySale = Transaction::factory()->for($otherListing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(3)->toDateString(),
        'end_date' => now()->addDays(4)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.inbox.sales', ['marketplace' => $marketplace])
        ->assertSee($sale->id)
        ->assertSee($listing->title)
        ->assertDontSee($otherListing->title);
});

it('shows sale details and activity log', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $sale = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);
    $activity = TransactionActivity::factory()->for($sale)->for($provider)->create([
        'type' => 'system',
        'description' => 'Sale created',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->assertSee($listing->title)
        ->assertSee('Sale Details')
        ->assertSee('Sale created');
});

it('provider can post a message and it appears in the activity log', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $sale = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->set('message', 'Hello buyer!')
        ->call('postMessage')
        ->assertHasNoErrors()
        ->assertSee('Hello buyer!')
        ->assertSee('You');

    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $sale->id,
        'type' => 'message',
        'description' => 'Hello buyer!',
        'user_id' => $provider->id,
    ]);
});

it('non-provider cannot post a message', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $sale = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);
    $otherUser = User::factory()->create();

    Volt::actingAs($otherUser)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->set('message', 'I should not be able to post')
        ->call('postMessage')
        ->assertStatus(403);
});
