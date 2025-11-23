<?php

use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\PayoutSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('requires account type and country', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', '')
        ->set('country', '')
        ->call('save')
        ->assertHasErrors(['accountType' => 'required', 'country' => 'required']);
});

it('rejects invalid account type and country', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'not-a-valid-type')
        ->set('country', 'ZZ')
        ->call('save')
        ->assertHasErrors(['accountType', 'country']);
});

it('persists payout settings for the correct user and marketplace', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Mock StripeConnectService::createAccount
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    $mock->shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Assert the settings are persisted for this user and marketplace
    expect(\Illuminate\Support\Facades\DB::table('payout_settings')->where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_fake123',
    ])->exists())->toBeTrue();

    Mockery::close();
});

it('cannot change account type or country after they are set', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // First call: mock StripeConnectService
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    $mock->shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    // Set initial values
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    Mockery::close(); // Clean up the mock

    // Second call: do NOT mock Stripe, should not be called
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'company')
        ->set('country', 'GB')
        ->call('save')
        ->assertHasNoErrors(); // No error, but values should not change

    // Assert the values did not change
    $row = \Illuminate\Support\Facades\DB::table('payout_settings')->where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
    ])->first();
    expect($row->account_type)->toBe('individual');
    expect($row->country)->toBe('US');
    expect($row->stripe_account_id)->toBe('acct_fake123');
});

// --- Onboarding TDD tests ---

it('cannot start onboarding without payout settings', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertHasErrors(['payout_settings' => 'required']);
});

it('can start onboarding when payout settings are configured', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Mock StripeConnectService::createAccount and createAccountLink
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $fakeAccountLink = (object) ['url' => 'https://connect.stripe.com/onboarding/test'];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    $mock->shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    $mock->shouldReceive('createAccountLink')->andReturn($fakeAccountLink);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    // Save payout settings
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Start onboarding
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');
});

it('tracks onboarding state per user and marketplace', function () {
    $organization = Organization::factory()->create();
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $organization->addMember($user1);
    $organization->addMember($user2);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Mock StripeConnectService::createAccount and createAccountLink for both users
    $fakeStripeAccount1 = (object) ['id' => 'acct_fake1'];
    $fakeStripeAccount2 = (object) ['id' => 'acct_fake2'];
    $fakeAccountLink = (object) ['url' => 'https://connect.stripe.com/onboarding/test'];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('createAccount')->andReturn($fakeStripeAccount1, $fakeStripeAccount2);
    $mock->shouldReceive('getAccount')->andReturn(
        (object) [
            'id' => 'acct_fake1',
            'charges_enabled' => false,
            'details_submitted' => false,
        ],
        (object) [
            'id' => 'acct_fake2',
            'charges_enabled' => false,
            'details_submitted' => false,
        ]
    );
    $mock->shouldReceive('createAccountLink')->andReturn($fakeAccountLink);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    // User 1 saves payout settings and starts onboarding
    Livewire::actingAs($user1)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();
    Livewire::actingAs($user1)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');

    // User 2 saves payout settings and starts onboarding
    Livewire::actingAs($user2)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'company')
        ->set('country', 'GB')
        ->call('save')
        ->assertHasNoErrors();
    Livewire::actingAs($user2)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');
});

it('can mark onboarding as completed', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Mock StripeConnectService::createAccount and createAccountLink
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $fakeAccountLink = (object) ['url' => 'https://connect.stripe.com/onboarding/test'];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    $mock->shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    $mock->shouldReceive('createAccountLink')->andReturn($fakeAccountLink);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    // Save payout settings
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Start onboarding
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress')
        ->call('completeOnboarding')
        ->assertSet('onboarding_status', 'completed');
});

it('redirects to Stripe onboarding after account creation', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Mock StripeConnectService::createAccount and createAccountLink
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $fakeOnboardingUrl = 'https://connect.stripe.com/onboarding/test';
    $fakeAccountLink = (object) ['url' => $fakeOnboardingUrl];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    $mock->shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    $mock->shouldReceive('createAccountLink')->andReturn($fakeAccountLink);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    // Save payout settings
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Start onboarding, expect redirect
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertRedirect($fakeOnboardingUrl);

    Mockery::close();
});

// --- Stripe onboarding status and URL TDD ---

it('redirects to Stripe Express dashboard after onboarding is complete', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Save payout settings with a Stripe account id and completed onboarding
    $setting = PayoutSetting::create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => 'completed',
    ]);

    // Mock StripeConnectService::getAccount and createExpressDashboardLink
    $fakeStripeAccount = (object) [
        'id' => 'acct_test123',
        'charges_enabled' => true,
        'details_submitted' => true,
    ];
    $dashboardUrl = 'https://connect.stripe.com/express/test-dashboard';
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('getAccount')->with('acct_test123')->andReturn($fakeStripeAccount);
    $mock->shouldReceive('createExpressDashboardLink')->with('acct_test123')->andReturn($dashboardUrl);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('redirectToStripeDashboard')
        ->assertRedirect($dashboardUrl);

    Mockery::close();
});

it('fetches latest onboarding status from Stripe on mount and sets completed if charges_enabled and details_submitted are true', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Save payout settings with a Stripe account id
    $setting = PayoutSetting::create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => null,
    ]);

    // Mock StripeConnectService::getAccount
    $fakeStripeAccount = (object) [
        'id' => 'acct_test123',
        'charges_enabled' => true,
        'details_submitted' => true,
    ];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('getAccount')->with('acct_test123')->andReturn($fakeStripeAccount);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->assertSet('onboarding_status', 'completed');

    Mockery::close();
});

it('fetches latest onboarding status from Stripe on mount and sets in_progress if not completed', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Save payout settings with a Stripe account id
    $setting = PayoutSetting::create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => null,
    ]);

    // Mock StripeConnectService::getAccount
    $fakeStripeAccount = (object) [
        'id' => 'acct_test123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ];
    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('getAccount')->with('acct_test123')->andReturn($fakeStripeAccount);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->assertSet('onboarding_status', 'in_progress');

    Mockery::close();
});

it('uses payout settings route as refresh and return URLs without query strings', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // Save payout settings with a Stripe account id
    $setting = PayoutSetting::create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => null,
    ]);

    $mock = Mockery::mock(\App\Services\StripeConnectService::class);
    $mock->shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_test123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    $mock->shouldReceive('createAccountLink')
        ->withArgs(function ($accountId, $refreshUrl, $returnUrl) use ($marketplace) {
            // The URLs should not contain a query string
            expect($refreshUrl)->toBe(route('on-marketplace.account.settings.payout', ['marketplace' => $marketplace]));
            expect($returnUrl)->toBe(route('on-marketplace.account.settings.payout', ['marketplace' => $marketplace]));
            expect(str_contains($refreshUrl, '?'))->toBeFalse();
            expect(str_contains($returnUrl, '?'))->toBeFalse();

            return true;
        })
        ->andReturn((object) ['url' => 'https://connect.stripe.com/onboarding/test']);
    app()->instance(\App\Services\StripeConnectService::class, $mock);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding');

    Mockery::close();
});
