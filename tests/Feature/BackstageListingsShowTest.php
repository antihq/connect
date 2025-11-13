<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

describe('Backstage Listing Show', function () {
    it('shows listing details for user\'s marketplace', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create([
            'title' => 'Test Listing',
            'description' => 'A great listing',
            'price' => 123.45,
        ]);
        $user->current_organization_id = $org->id;
        $user->save();

        actingAs($user);
        $response = get(route('backstage.listings.show', $listing));
        $response->assertOk();
        $response->assertSee('Test Listing');
        $response->assertSee('A great listing');
        $response->assertSee('123.45');
    });

    it('returns 404 for listing from another marketplace', function () {
        $user = User::factory()->create();
        $org = Organization::factory()->for($user)->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();
        $otherOrg = Organization::factory()->create();
        $otherMarketplace = Marketplace::factory()->for($otherOrg)->create();
        $otherListing = Listing::factory()->for($otherMarketplace)->create();
        $user->current_organization_id = $org->id;
        $user->save();

        actingAs($user);
        $response = get(route('backstage.listings.show', $otherListing));
        $response->assertNotFound();
    });

    it('redirects guest to login', function () {
        $org = Organization::factory()->create();
        $marketplace = Marketplace::factory()->for($org)->create();
        $listing = Listing::factory()->for($marketplace)->create();

        $response = get(route('backstage.listings.show', $listing));
        $response->assertRedirect(route('login'));
    });

    it('returns 404 for non-existent listing', function () {
        $user = User::factory()->create();
        actingAs($user);
        $response = get('/backstage/listings/999999');
        $response->assertNotFound();
    });
});
