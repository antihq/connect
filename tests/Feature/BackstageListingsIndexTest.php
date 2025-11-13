<?php

use App\Models\Listing;
use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\User;
use Livewire\Volt\Volt;

it('shows all listings for the user\'s current organization marketplace', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create();
    $listings = Listing::factory()->count(3)->for($marketplace)->create();

    // Listing from another org/marketplace
    $otherMarketplace = Marketplace::factory()->create();
    Listing::factory()->for($otherMarketplace)->create(['title' => 'Other Listing']);

    $user->current_organization_id = $org->id;
    $user->save();

    Volt::actingAs($user)
        ->test('backstage.listings.index')
        ->assertSee($listings->pluck('title')->all())
        ->assertDontSee('Other Listing');
});

it('shows empty state if no listings exist', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    Marketplace::factory()->for($org)->create();

    $user->current_organization_id = $org->id;
    $user->save();

    Volt::actingAs($user)
        ->test('backstage.listings.index')
        ->assertSee('No listings found'); // Adjust message to match your UI
});

it('shows only listings for the current organization when user has multiple orgs', function () {
    $user = User::factory()->create();
    $org1 = Organization::factory()->for($user)->create();
    $org2 = Organization::factory()->for($user)->create();
    $marketplace1 = Marketplace::factory()->for($org1)->create();
    $marketplace2 = Marketplace::factory()->for($org2)->create();
    Listing::factory()->for($marketplace1)->create(['title' => 'Org1 Listing']);
    Listing::factory()->for($marketplace2)->create(['title' => 'Org2 Listing']);

    $user->current_organization_id = $org2->id;
    $user->save();

    Volt::actingAs($user)
        ->test('backstage.listings.index')
        ->assertSee('Org2 Listing')
        ->assertDontSee('Org1 Listing');
});

it('redirects guests to login', function () {
    $this->get(route('backstage.listings.index'))
        ->assertRedirect(route('login'));
});
