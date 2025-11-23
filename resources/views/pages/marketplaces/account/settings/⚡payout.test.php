<?php

use App\Models\Marketplace;
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

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'company')
        ->set('country', 'GB')
        ->call('save')
        ->assertHasNoErrors();

    $setting = PayoutSetting::where([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
    ])->first();
    expect($setting)->not->toBeNull();
    expect($setting->account_type)->toBe('individual');
    expect($setting->country)->toBe('US');
    expect($setting->stripe_account_id)->toBe('acct_fake123');
});

it('cannot start onboarding without payout settings', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertHasErrors(['payout_settings' => 'required']);
});

it('can start onboarding when payout settings are configured', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $fakeAccountLink = (object) ['url' => 'https://connect.stripe.com/onboarding/test'];
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    StripeConnectService::shouldReceive('createAccountLink')->andReturn($fakeAccountLink);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertSet('onboarding_status', 'in_progress');
});

it('can mark onboarding as completed', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $fakeStripeAccount = (object) ['id' => 'acct_fake123'];
    $fakeAccountLink = (object) ['url' => 'https://connect.stripe.com/onboarding/test'];
    StripeConnectService::shouldReceive('createAccount')->andReturn($fakeStripeAccount);
    StripeConnectService::shouldReceive('getAccount')->andReturn((object) [
        'id' => 'acct_fake123',
        'charges_enabled' => false,
        'details_submitted' => false,
    ]);
    StripeConnectService::shouldReceive('createAccountLink')->andReturn($fakeAccountLink);

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

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
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

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

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->set('accountType', 'individual')
        ->set('country', 'US')
        ->call('save')
        ->assertHasNoErrors();

    Livewire::actingAs($user)
        ->test('pages::marketplaces.account.settings.payout', [
            'marketplace' => $marketplace,
        ])
        ->call('startOnboarding')
        ->assertRedirect($fakeOnboardingUrl);

});

it('redirects to Stripe Express dashboard after onboarding is complete', function () {
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $setting = PayoutSetting::factory()->create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => 'completed',
    ]);

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
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $setting = PayoutSetting::factory()->create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => null,
    ]);

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
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $setting = PayoutSetting::factory()->create([
        'user_id' => $user->id,
        'marketplace_id' => $marketplace->id,
        'account_type' => 'individual',
        'country' => 'US',
        'stripe_account_id' => 'acct_test123',
        'onboarding_status' => null,
    ]);

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
    $user = User::factory()->create();
    $marketplace = Marketplace::factory()->create();

    $setting = PayoutSetting::factory()->create([
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
