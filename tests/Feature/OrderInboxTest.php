<?php

use App\Models\User;
use App\Models\Marketplace;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Livewire\Volt\Volt;
use function Pest\Laravel\assertDatabaseHas;

it('shows only the user\'s orders in the inbox', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $otherUser = User::factory()->create();
    $order = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);
    $notMyOrder = Transaction::factory()->for($listing)->for($otherUser)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(3)->toDateString(),
        'end_date' => now()->addDays(4)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);

    Volt::actingAs($user)
        ->test('marketplaces.inbox.orders', ['marketplace' => $marketplace])
        ->assertSee($order->id)
        ->assertSee($listing->title)
        ->assertDontSee($notMyOrder->start_date);
});

it('shows order details and activity log', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $order = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);
    $activity = TransactionActivity::factory()->for($order)->for($user)->create([
        'type' => 'system',
        'description' => 'Order created',
    ]);

    Volt::actingAs($user)
        ->test('marketplaces.orders.show', ['marketplace' => $marketplace, 'transaction' => $order])
        ->assertSee($listing->title)
        ->assertSee('Order Details')
        ->assertSee('Order created');
});

it('buyer can post a message and it appears in the activity log', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $order = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);

    Volt::actingAs($user)
        ->test('marketplaces.orders.show', ['marketplace' => $marketplace, 'transaction' => $order])
        ->set('message', 'Hello provider!')
        ->call('postMessage')
        ->assertHasNoErrors()
        ->assertSee('Hello provider!')
        ->assertSee('You');

    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $order->id,
        'type' => 'message',
        'description' => 'Hello provider!',
        'user_id' => $user->id,
    ]);
});

it('non-buyer cannot post a message', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $order = Transaction::factory()->for($listing)->for(User::factory()->create())->create([
        'marketplace_id' => $marketplace->id,
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
        'status' => 'pending',
    ]);

    Volt::actingAs($user)
        ->test('marketplaces.orders.show', ['marketplace' => $marketplace, 'transaction' => $order])
        ->set('message', 'I should not be able to post')
        ->call('postMessage')
        ->assertStatus(403);
});
