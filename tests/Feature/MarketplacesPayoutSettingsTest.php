<?php

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

it('requires account type and country', function () {
    $organization = \App\Models\Organization::factory()->create();
    $user = \App\Models\User::factory()->create();
    $organization->addMember($user);
    $marketplace = \App\Models\Marketplace::factory()->for($organization)->create();

    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', null)
        ->set('country', null)
        ->call('save')
        ->assertHasErrors(['accountType' => 'required', 'country' => 'required']);
});

it('rejects invalid account type and country', function () {
    $organization = \App\Models\Organization::factory()->create();
    $user = \App\Models\User::factory()->create();
    $organization->addMember($user);
    $marketplace = \App\Models\Marketplace::factory()->for($organization)->create();

    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'not-a-valid-type')
        ->set('country', 'ZZ')
        ->call('save')
        ->assertHasErrors(['accountType', 'country']);
});

it('persists payout settings for the correct user and marketplace', function () {
    $organization = \App\Models\Organization::factory()->create();
    $user = \App\Models\User::factory()->create();
    $organization->addMember($user);
    $marketplace = \App\Models\Marketplace::factory()->for($organization)->create();

    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Assert the settings are persisted for this user and marketplace
    expect(\Illuminate\Support\Facades\DB::table('marketplace_payout_settings')->where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
    ])->exists())->toBeTrue();
});
