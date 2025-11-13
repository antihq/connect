<?php

use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('shows the sender email name edit form for organization user', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['sender_email_name' => 'Acme Marketplace']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.email')
        ->assertSet('sender_email_name', 'Acme Marketplace');
});

it('allows organization user to update the sender email name', function () {
    $user = User::factory()->create();
    $org = Organization::factory()->for($user)->create();
    $marketplace = Marketplace::factory()->for($org)->create(['sender_email_name' => 'Old Sender']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.email')
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

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.email')
        ->set('sender_email_name', '')
        ->call('save')
        ->assertHasErrors(['sender_email_name' => 'required']);

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.email')
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

    Volt::actingAs($user)
        ->test('backstage.marketplaces.settings.email')
        ->call('save')
        ->assertForbidden();
});
