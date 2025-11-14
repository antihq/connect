<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use App\Models\User;
use Livewire\Volt\Volt;

use function Pest\Laravel\assertDatabaseHas;

it('customer can review the provider after transaction is completed', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $order = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'completed',
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
    ]);

    Volt::actingAs($buyer)
        ->test('marketplaces.orders.show', ['marketplace' => $marketplace, 'transaction' => $order])
        ->set('review_rating', 4)
        ->set('review_comment', 'Great provider!')
        ->call('submitReview')
        ->assertHasNoErrors()
        ->assertSee('Review submitted')
        ->assertSee('Great provider!');

    assertDatabaseHas('reviews', [
        'transaction_id' => $order->id,
        'reviewer_id' => $buyer->id,
        'reviewee_id' => $provider->id,
        'rating' => 4,
        'comment' => 'Great provider!',
    ]);
    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $order->id,
        'type' => 'review',
        'user_id' => $buyer->id,
        'description' => 'Customer reviewed the provider: Great provider!',
    ]);
});

it('customer cannot review the provider unless transaction is completed', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $order = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'pending',
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
    ]);

    Volt::actingAs($buyer)
        ->test('marketplaces.orders.show', ['marketplace' => $marketplace, 'transaction' => $order])
        ->set('review_rating', 3)
        ->set('review_comment', 'Not yet complete')
        ->call('submitReview')
        ->assertStatus(403);
});

it('customer cannot review the provider more than once', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $order = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'completed',
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
    ]);
    // Simulate already reviewed
    TransactionActivity::factory()->for($order)->for($buyer)->create([
        'type' => 'review',
    ]);

    Volt::actingAs($buyer)
        ->test('marketplaces.orders.show', ['marketplace' => $marketplace, 'transaction' => $order])
        ->set('review_rating', 5)
        ->set('review_comment', 'Trying again')
        ->call('submitReview')
        ->assertStatus(403);
});

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
        ->assertHasNoErrors();

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
