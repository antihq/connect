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

    // Mock Stripe\Account::create
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    Mockery::mock('overload:\\Stripe\\Account')
        ->shouldReceive('create')
        ->once()
        ->andReturn($fakeStripeAccount);

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
        'stripe_account_id' => 'acct_fake123',
    ])->exists())->toBeTrue();

    Mockery::close();
});

it('cannot change account type or country after they are set', function () {
    $organization = \App\Models\Organization::factory()->create();
    $user = \App\Models\User::factory()->create();
    $organization->addMember($user);
    $marketplace = \App\Models\Marketplace::factory()->for($organization)->create();

    // First call: mock Stripe
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    Mockery::mock('overload:\\Stripe\\Account')
        ->shouldReceive('create')
        ->once()
        ->andReturn($fakeStripeAccount);

    // Set initial values
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    Mockery::close(); // Clean up the mock

    // Second call: do NOT mock Stripe, should not be called
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'company')
        ->set('country', 'GB')
        ->call('save')
        ->assertHasNoErrors(); // No error, but values should not change

    // Assert the values did not change
    $row = \Illuminate\Support\Facades\DB::table('marketplace_payout_settings')->where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
    ])->first();
    expect($row->account_type)->toBe('individual');
    expect($row->country)->toBe('US');
    expect($row->stripe_account_id)->toBe('acct_fake123');
});
