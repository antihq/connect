<?php

use App\Models\Marketplace;
use App\Models\Organization;
use App\Models\PayoutSetting;
use App\Models\User;
use Facades\App\Services\StripeConnectService;
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
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Assert the settings are persisted for this user and marketplace
    $setting = PayoutSetting::where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
    ])->first();
    expect($setting)->not->toBeNull();
    expect($setting->account_type)->toBe('individual');
    expect($setting->country)->toBe('US');
    expect($setting->stripe_account_id)->toBe('acct_fake123');
});

it('cannot change account type or country after they are set', function () {
    $organization = Organization::factory()->create();
    $user = User::factory()->create();
    $organization->addMember($user);
    $marketplace = Marketplace::factory()->for($organization)->create();

    // First call: mock StripeConnectService
    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);

    // Set initial values
    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    // Clean up the mock

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
    $setting = PayoutSetting::where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
    ])->first();
    expect($setting)->not->toBeNull();
    expect($setting->account_type)->toBe('individual');
    expect($setting->country)->toBe('US');
    expect($setting->stripe_account_id)->toBe('acct_fake123');
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
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    StripeConnectService::shouldReceive('createAccountLink')->andReturn($fakeAccountLink);

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
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount1, $fakeStripeAccount2);
    StripeConnectService::shouldReceive('getAccount')->andReturn(
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
    StripeConnectService::shouldReceive('createAccountLink')->andReturn($fakeAccountLink);

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
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    StripeConnectService::shouldReceive('createAccountLink')->andReturn($fakeAccountLink);

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
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    StripeConnectService::shouldReceive('createAccountLink')->andReturn($fakeAccountLink);

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
    StripeConnectService::shouldReceive('getAccount')->with('acct_test123')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('createExpressDashboardLink')->with('acct_test123')->andReturn($dashboardUrl);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('redirectToStripeDashboard')
        ->assertRedirect($dashboardUrl);

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
    StripeConnectService::shouldReceive('getAccount')->with('acct_test123')->andReturn($fakeStripeAccount);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->assertSet('onboarding_status', 'completed');

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
    StripeConnectService::shouldReceive('getAccount')->with('acct_test123')->andReturn($fakeStripeAccount);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->assertSet('onboarding_status', 'in_progress');

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

    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    StripeConnectService::shouldReceive('createAccountLink')
        ->withArgs(function ($accountId, $refreshUrl, $returnUrl) use ($marketplace) {
            // The URLs should not contain a query string
            expect($refreshUrl)->toBe(route('on-marketplace.account.settings.payout', ['marketplace' => $marketplace]));
            expect($returnUrl)->toBe(route('on-marketplace.account.settings.payout', ['marketplace' => $marketplace]));
            expect(str_contains($refreshUrl, '?'))->toBeFalse();
            expect(str_contains($returnUrl, '?'))->toBeFalse();

            return true;
        })
        ->andReturn((object) ['url' => 'https://connect.stripe.com/onboarding/test']);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding');

});
