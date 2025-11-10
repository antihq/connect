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


it('shows the marketplace slug edit form for organization user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'original-slug']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->assertSet('slug', 'original-slug');
});

it('allows organization user to update the marketplace slug', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'old-slug']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
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

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->set('slug', 'taken-slug')
        ->call('save')
        ->assertHasErrors(['slug' => 'unique']);
});

it('validates that the marketplace slug is required and alpha_dash', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['slug' => 'valid-slug']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['slug' => 'required']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.name')
        ->set('slug', 'invalid slug!')
        ->call('save')
        ->assertHasErrors(['slug' => 'alpha_dash']);
});

