<?php

use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('allows organization user to set a custom domain for their marketplace', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);

    Livewire::actingAs($user)
        ->test('backstage.marketplaces.settings.domain')
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
        ->test('backstage.marketplaces.settings.domain')
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
        ->test('backstage.marketplaces.settings.domain')
        ->set('domain', 'taken.com')
        ->call('save')
        ->assertHasErrors(['domain' => 'unique']);
});

it('validates that the domain is a valid format', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['domain' => null]);

    Livewire::actingAs($user)
        ->test('backstage.marketplaces.settings.domain')
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
        ->test('backstage.marketplaces.settings.domain')
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
        ->test('backstage.marketplaces.settings.domain')
        ->call('save')
        ->assertForbidden();
});
