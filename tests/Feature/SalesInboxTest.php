<?php
use App\Models\User;
use App\Models\Marketplace;
use App\Models\Listing;
use App\Models\Transaction;
use App\Models\TransactionActivity;
use Livewire\Volt\Volt;
use function Pest\Laravel\assertDatabaseHas;

it('provider can review the customer after transaction is completed', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $sale = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'completed',
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->set('review_rating', 5)
        ->set('review_comment', 'Great customer!')
        ->call('submitReview')
        ->assertHasNoErrors()
        ->assertSee('Review submitted')
        ->assertSee('Great customer!');

    assertDatabaseHas('reviews', [
        'transaction_id' => $sale->id,
        'reviewer_id' => $provider->id,
        'reviewee_id' => $buyer->id,
        'rating' => 5,
        'comment' => 'Great customer!',
    ]);
    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $sale->id,
        'type' => 'review',
        'user_id' => $provider->id,
        'description' => 'Provider reviewed the customer: Great customer!',
    ]);
});

it('provider cannot review the customer unless transaction is completed', function () {
    $provider = User::factory()->create();
    $marketplace = Marketplace::factory()->create();
    $listing = Listing::factory()->for($marketplace)->for($provider)->create();
    $buyer = User::factory()->create();
    $sale = Transaction::factory()->for($listing)->for($buyer)->create([
        'marketplace_id' => $marketplace->id,
        'status' => 'completed',
        'start_date' => now()->addDays(1)->toDateString(),
        'end_date' => now()->addDays(2)->toDateString(),
        'nights' => 1,
        'price_per_night' => 100,
        'total' => 100,
    ]);
    // Simulate already reviewed
    TransactionActivity::factory()->for($sale)->for($provider)->create([
        'type' => 'review',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->set('review_rating', 5)
        ->set('review_comment', 'Another review')
        ->call('submitReview')
        ->assertStatus(403);
});



it('provider can mark an accepted transaction as complete', function () {
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
        'status' => 'accepted',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('markAsComplete')
        ->assertHasNoErrors()
        ->assertSee('completed')
        ->assertSee('Provider marked the transaction as completed.');

    expect($sale->fresh()->status)->toBe('completed');
    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $sale->id,
        'type' => 'status_change',
        'description' => 'Provider marked the transaction as completed.',
        'user_id' => $provider->id,
    ]);
});

it('non-provider cannot mark as complete', function () {
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
        'status' => 'accepted',
    ]);
    $otherUser = User::factory()->create();

    Volt::actingAs($otherUser)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('markAsComplete')
        ->assertStatus(403);
});

it('provider cannot mark as complete unless status is accepted', function () {
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
        'status' => 'paid',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('markAsComplete')
        ->assertStatus(403);
});


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

it('provider can accept a paid transaction', function () {
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
        'status' => 'paid',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('acceptRequest')
        ->assertHasNoErrors()
        ->assertSee('accepted')
        ->assertSee('Provider accepted the request.');

    expect($sale->fresh()->status)->toBe('accepted');
    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $sale->id,
        'type' => 'status_change',
        'description' => 'Provider accepted the request.',
        'user_id' => $provider->id,
    ]);
});

it('provider can reject a paid transaction', function () {
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
        'status' => 'paid',
    ]);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('rejectRequest')
        ->assertHasNoErrors()
        ->assertSee('rejected')
        ->assertSee('Provider rejected the request.');

    expect($sale->fresh()->status)->toBe('rejected');
    assertDatabaseHas('transaction_activities', [
        'transaction_id' => $sale->id,
        'type' => 'status_change',
        'description' => 'Provider rejected the request.',
        'user_id' => $provider->id,
    ]);
});

it('non-provider cannot accept or reject', function () {
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
        'status' => 'paid',
    ]);
    $otherUser = User::factory()->create();

    Volt::actingAs($otherUser)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('acceptRequest')
        ->assertStatus(403);

    Volt::actingAs($otherUser)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('rejectRequest')
        ->assertStatus(403);
});

it('provider cannot accept or reject unless status is paid', function () {
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
        ->call('acceptRequest')
        ->assertStatus(403);

    Volt::actingAs($provider)
        ->test('marketplaces.sales.show', ['marketplace' => $marketplace, 'transaction' => $sale])
        ->call('rejectRequest')
        ->assertStatus(403);
});
