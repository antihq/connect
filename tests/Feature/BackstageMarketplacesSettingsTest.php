<?php

use App\Models\User;
use App\Models\Organization;
use App\Models\Marketplace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('shows the marketplace name edit form for organization user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['name' => 'Original Name']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->assertSet('name', 'Original Name');
});

it('allows organization user to update the marketplace name', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['name' => 'Old Name']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
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

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

it('forbids non-organization user from updating the marketplace name', function () {
    $user = User::factory()->create();
    $otherOrg = Organization::factory()->for($user)->create(); // user belongs to this org
    $org = Organization::factory()->create(); // marketplace belongs to this org
    $marketplace = Marketplace::factory()->for($org)->create(['name' => 'Original Name']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->set('name', 'Hacked Name')
        ->call('save')
        ->assertForbidden();

    $marketplace->refresh();
    expect($marketplace->name)->toBe('Original Name');
});
