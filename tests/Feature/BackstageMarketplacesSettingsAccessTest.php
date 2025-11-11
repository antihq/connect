<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Marketplace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('allows organization owner to view the access control settings page', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create();

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->assertOk();
});

it('allows owner to update is_private', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['is_private' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('is_private', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->is_private)->toBeTrue();
});

it('allows owner to update require_user_approval', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['require_user_approval' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->require_user_approval)->toBeTrue();
});

it('allows owner to update restrict_view_listings', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['restrict_view_listings' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('restrict_view_listings', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->restrict_view_listings)->toBeTrue();
});

it('allows owner to update restrict_posting', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['restrict_posting' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('restrict_posting', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->restrict_posting)->toBeTrue();
});

it('allows owner to update restrict_transactions', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['restrict_transactions' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('restrict_transactions', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->restrict_transactions)->toBeTrue();
});

it('allows owner to update require_listing_approval', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['require_listing_approval' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_listing_approval', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->require_listing_approval)->toBeTrue();
});

it('prevents users from updating access settings for marketplaces they do not own', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->for($org)->create();
    $user->switchOrganization($org);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('is_private', true)
        ->call('save')
        ->assertForbidden();
});

it('shows all toggles with correct state', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'is_private' => true,
        'require_user_approval' => true,
        'restrict_view_listings' => true,
        'restrict_posting' => true,
        'restrict_transactions' => true,
        'require_listing_approval' => true,
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->assertSet('is_private', true)
        ->assertSet('require_user_approval', true)
        ->assertSet('restrict_view_listings', true)
        ->assertSet('restrict_posting', true)
        ->assertSet('restrict_transactions', true)
        ->assertSet('require_listing_approval', true);
});
