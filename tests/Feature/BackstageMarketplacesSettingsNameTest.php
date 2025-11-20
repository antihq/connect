<?php

use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows the marketplace name edit form for organization user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['name' => 'Original Name']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->assertSet('name', 'Original Name');
});

it('allows organization user to update the marketplace name', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['name' => 'Old Name']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->name)->toBe('New Name');
});

it('shows validation errors for invalid input', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['name' => 'Valid Name']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('shows the marketplace slug edit form for organization user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'original-slug']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->assertSet('slug', 'original-slug');
});

it('allows organization user to update the marketplace slug', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'old-slug']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->set('slug', 'new-slug')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->slug)->toBe('new-slug');
});

it('validates that the marketplace slug is unique', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'unique-slug']);
    $otherMarketplace = Marketplace::factory()->create(['slug' => 'taken-slug']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->set('slug', 'taken-slug')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

it('validates that the marketplace slug is required and alpha_dash', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'valid-slug']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['slug' => 'required']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.name')
        ->set('slug', 'invalid slug!')
        ->call('save')
        ->assertHasErrors(['slug' => 'alpha_dash']);
});

// Domain settings tests

// Sender Email Name settings tests

it('shows the sender email name edit form for organization user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['sender_email_name' => 'Acme Marketplace']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.email')
        ->assertSet('sender_email_name', 'Acme Marketplace');
});

it('allows organization user to update the sender email name', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['sender_email_name' => 'Old Sender']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.email')
        ->set('sender_email_name', 'New Sender')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->sender_email_name)->toBe('New Sender');
});

it('shows validation errors for invalid sender email name', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['sender_email_name' => 'Valid Sender']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.email')
        ->set('sender_email_name', '')
        ->call('save')
        ->assertHasErrors(['sender_email_name' => 'required']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.email')
        ->set('sender_email_name', str_repeat('a', 256))
        ->call('save')
        ->assertHasErrors(['sender_email_name' => 'max']);
});

it('prevents users from accessing the sender email name settings for marketplaces they do not own', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['sender_email_name' => 'Other Sender']);
    $user->switchOrganization($org);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.email')
        ->call('save')
        ->assertForbidden();
});

it('allows organization user to set a custom domain for their marketplace', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.domain')
        ->set('domain', 'custom-domain.com')
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->domain)->toBe('custom-domain.com');
});

it('allows domain to be nullable', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => 'something.com']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.domain')
        ->set('domain', null)
        ->call('save')
        ->assertHasNoErrors();

    $marketplace->refresh();
    expect($marketplace->domain)->toBeNull();
});

it('validates that the domain is unique', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);
    $otherMarketplace = Marketplace::factory()->create(['domain' => 'taken.com']);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.domain')
        ->set('domain', 'taken.com')
        ->call('save')
        ->assertHasErrors(['domain' => 'unique']);
});

it('validates that the domain is a valid format', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.domain')
        ->set('domain', 'not a domain!')
        ->call('save')
        ->assertHasErrors(['domain']);
});

it('validates that the domain is not too long', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);
    $longDomain = str_repeat('a', 256).'.com';

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.domain')
        ->set('domain', $longDomain)
        ->call('save')
        ->assertHasErrors(['domain' => 'max']);
});

it('prevents users from accessing the domain settings for marketplaces they do not own', function () {
    $user = User::factory()->create();
    $owner = User::factory()->create();
    $org = Organization::factory()->for($owner)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);
    $user->switchOrganization($org);

    Livewire::actingAs($user)
        ->test('pages::backstage.marketplaces.settings.domain')
        ->call('save')
        ->assertForbidden();
});
