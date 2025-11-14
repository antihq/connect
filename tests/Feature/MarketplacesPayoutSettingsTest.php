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

// --- Onboarding TDD tests ---

it('cannot start onboarding without payout settings', function () {
    $organization = \App\Models\Organization::factory()->create();
    $user = \App\Models\User::factory()->create();
    $organization->addMember($user);
    $marketplace = \App\Models\Marketplace::factory()->for($organization)->create();

    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertHasErrors(['payout_settings' => 'required']);
});

it('can start onboarding when payout settings are configured', function () {
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

    // Save payout settings
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();
    Mockery::close();

    // Start onboarding
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');
});

it('tracks onboarding state per user and marketplace', function () {
    $organization = \App\Models\Organization::factory()->create();
    $user1 = \App\Models\User::factory()->create();
    $user2 = \App\Models\User::factory()->create();
    $organization->addMember($user1);
    $organization->addMember($user2);
    $marketplace = \App\Models\Marketplace::factory()->for($organization)->create();

    // Mock Stripe\Account::create for both users
    $fakeStripeAccount1 = (object) ['id' => 'acct_fake1'];
    $fakeStripeAccount2 = (object) ['id' => 'acct_fake2'];
    $mock = Mockery::mock('overload:\\Stripe\\Account');
    $mock->shouldReceive('create')->andReturn($fakeStripeAccount1, $fakeStripeAccount2);

    // User 1 saves payout settings and starts onboarding
    Volt::actingAs($user1)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();
    Volt::actingAs($user1)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');

    // User 2 saves payout settings and starts onboarding
    Volt::actingAs($user2)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'company')
        ->set('country', 'GB')
        ->call('save')
        ->assertHasNoErrors();
    Volt::actingAs($user2)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');
    Mockery::close();
});

it('can mark onboarding as completed', function () {
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

    // Save payout settings
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();
    Mockery::close();

    // Start onboarding
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress')
        ->call('completeOnboarding')
        ->assertSet('onboarding_status', 'completed');
});

it('redirects to Stripe onboarding after account creation', function () {
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

    // Mock Stripe\AccountLink::create
    $fakeOnboardingUrl = 'https://connect.stripe.com/onboarding/test';
    $fakeAccountLink = (object) ['url' => $fakeOnboardingUrl];
    Mockery::mock('overload:\\Stripe\\AccountLink')
        ->shouldReceive('create')
        ->once()
        ->andReturn($fakeAccountLink);

    // Save payout settings
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Start onboarding, expect redirect
    Volt::actingAs($user)
        ->test('marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertRedirect($fakeOnboardingUrl);

    Mockery::close();
});

