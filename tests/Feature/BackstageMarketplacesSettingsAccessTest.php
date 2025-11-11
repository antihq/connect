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
    $marketplace = Marketplace::factory()->for($org)->create(['restrict_view_listings' => false, 'is_private' => false]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('is_private', true)
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

it('prevents setting restrict_view_listings to true when is_private is false', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'is_private' => false,
        'restrict_view_listings' => false,
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('restrict_view_listings', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->restrict_view_listings)->toBeFalse();
});

it('allows setting restrict_view_listings to true only when is_private is true', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'is_private' => true,
        'restrict_view_listings' => false,
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('restrict_view_listings', true)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->restrict_view_listings)->toBeTrue();
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

// --- New TDD tests for require_user_approval call-to-action settings ---
it('allows owner to set require_user_approval_action to none, internal, or external', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'require_user_approval' => true,
        'require_user_approval_action' => 'none',
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'internal')
        ->set('require_user_approval_internal_link', '/dashboard')
        ->set('require_user_approval_internal_text', 'Go to Dashboard')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->require_user_approval_action)->toBe('internal');
    expect($marketplace->require_user_approval_internal_link)->toBe('/dashboard');

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'external')
        ->set('require_user_approval_external_link', 'https://example.com')
        ->set('require_user_approval_external_text', 'Visit Site')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->require_user_approval_action)->toBe('external');
    expect($marketplace->require_user_approval_external_link)->toBe('https://example.com');

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'none')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->require_user_approval_action)->toBe('none');
});

it('requires internal link if require_user_approval_action is internal', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'require_user_approval' => true,
        'require_user_approval_action' => 'none',
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'internal')
        ->set('require_user_approval_internal_link', null)
        ->call('save')
        ->assertHasErrors(['require_user_approval_internal_link' => 'required_if']);
});

it('requires external link and valid url if require_user_approval_action is external', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'require_user_approval' => true,
        'require_user_approval_action' => 'none',
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'external')
        ->set('require_user_approval_external_link', null)
        ->call('save')
        ->assertHasErrors(['require_user_approval_external_link' => 'required_if']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'external')
        ->set('require_user_approval_external_link', 'not-a-url')
        ->call('save')
        ->assertHasErrors(['require_user_approval_external_link' => 'url']);
});

it('does not require links if require_user_approval_action is none', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create([
        'require_user_approval' => true,
        'require_user_approval_action' => 'none',
    ]);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.access')
        ->set('require_user_approval', true)
        ->set('require_user_approval_action', 'none')
        ->set('require_user_approval_internal_link', null)
        ->set('require_user_approval_external_link', null)
        ->call('save')
        ->assertHasNoErrors();
});

// // UI/Alpine/Livewire integration test would be in a browser test suite, but you can assert the fields are always present and disabled appropriately in a browser test.
