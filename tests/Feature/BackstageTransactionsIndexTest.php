<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Marketplace;
use App\Models\Listing;
use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('shows all transactions for user\'s marketplace', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();
        $transaction1 = Transaction::factory()->for($listing)->for($user)->create([
            'marketplace_id' => $marketplace->id,
            'total' => 100
        ]);
        $transaction2 = Transaction::factory()->for($listing)->for($user)->create([
            'marketplace_id' => $marketplace->id,
            'total' => 200
        ]);
        $user->current_organization_id = $org->id;
        $user->save();

        actingAs($user);
        $response = get(route('backstage.transactions.index'));
        $response->assertOk();
        $response->assertSee('100.00');
        $response->assertSee('200.00');
    });

    it('does not show transactions from other marketplaces', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();
        $transaction = Transaction::factory()->for($listing)->for($user)->create([
            'marketplace_id' => $marketplace->id,
            'total' => 100
        ]);
        $otherOrg = Organization::factory()->create();
        $otherMarketplace = Marketplace::factory()->for($otherOrg)->create();
        $otherListing = Listing::factory()->for($otherMarketplace)->create();
        $otherTransaction = Transaction::factory()->for($otherListing)->for($user)->create([
            'marketplace_id' => $otherMarketplace->id,
            'total' => 999
        ]);
        $user->current_organization_id = $org->id;
        $user->save();

        actingAs($user);
        $response = get(route('backstage.transactions.index'));
        $response->assertOk();
        $response->assertSee('100.00');
        $response->assertDontSee('999.00');
    });

    it('redirects guest to login', function () {
        $org = Organization::factory()->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();
        $transaction = Transaction::factory()->for($listing)->create();

        $response = get(route('backstage.transactions.index'));
        $response->assertRedirect(route('login'));
    });

    it('shows no transactions if user has no marketplace', function () {
        $user = User::factory()->create();
        actingAs($user);
        $response = get(route('backstage.transactions.index'));
        $response->assertOk();
        $response->assertSee('No transactions found');
    });

    it('shows no transactions if marketplace has none', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $user->current_organization_id = $org->id;
        $user->save();
        actingAs($user);
        $response = get(route('backstage.transactions.index'));
        $response->assertOk();
        $response->assertSee('No transactions found');
    });
