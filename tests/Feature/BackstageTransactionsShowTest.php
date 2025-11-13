<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('shows transaction details for authorized user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create();
    $listing = Listing::factory()->for($marketplace)->create([
        'title' => 'Test Listing',
    ]);
    $transaction = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
        'total' => 123.45,
        'status' => 'paid',
    ]);
    $user->current_organization_id = $org->id;
    $user->save();

    Volt::actingAs($user)
        ->test('backstage.transactions.show', ['transaction' => $transaction])
        ->assertSee('Test Listing')
        ->assertSee('123.45')
        ->assertSee('paid')
        ->assertSee($transaction->created_at->format('Y-m-d'));
});

it('returns 404 for transaction from another marketplace', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $transaction = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
    ]);
    $otherOrg = Organization::factory()->create();
    $otherMarketplace = Marketplace::factory()->for($otherOrg)->create();
    $otherListing = Listing::factory()->for($otherMarketplace)->create();
    $otherTransaction = Transaction::factory()->for($otherListing)->for($user)->create([
        'marketplace_id' => $otherMarketplace->id,
    ]);
    $user->current_organization_id = $org->id;
    $user->save();

    Volt::actingAs($user)
        ->test('backstage.transactions.show', ['transaction' => $otherTransaction])
        ->assertNotFound();
});

it('redirects guest to login', function () {
    $org = Organization::factory()->create();
    $marketplace = Marketplace::factory()->for($org)->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $transaction = Transaction::factory()->for($listing)->create();

    $response = get(route('backstage.transactions.show', $transaction));
    $response->assertRedirect(route('login'));
});

it('returns 404 for non-existent transaction', function () {
    $user = User::factory()->create();
    \Pest\Laravel\actingAs($user);
    $response = get(route('backstage.transactions.show', 999999));
    $response->assertNotFound();
});

it('handles missing related data gracefully', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create();
    $listing = Listing::factory()->for($marketplace)->create();
    $transaction = Transaction::factory()->for($listing)->for($user)->create([
        'marketplace_id' => $marketplace->id,
    ]);
    $listing->delete(); // Simulate missing related listing
    $user->current_organization_id = $org->id;
    $user->save();

    Volt::actingAs($user)
        ->test('backstage.transactions.show', ['transaction' => $transaction])
        ->assertSee('N/A');
});
