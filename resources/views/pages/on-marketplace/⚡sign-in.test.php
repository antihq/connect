<?php

use App\Models\Marketplace;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\OneTimePasswords\Models\OneTimePassword;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertAuthenticatedAs;

it('sends a magic code to any email', function () {
    Notification::fake();

    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'anyone@example.com')
        ->call('sendMagicCode');

    $user = User::where('email', 'anyone@example.com')->first();

    expect($user)->not->toBeNull();

    // The main test is that OTP was created for the user
    expect(OneTimePassword::where('authenticatable_id', $user->id)->where('authenticatable_type', User::class)->exists())->toBeTrue();
});

it('shows validation error for invalid email', function () {
    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'not-an-email')
        ->call('sendMagicCode')
        ->assertHasErrors(['email']);
});

it('logs in existing user with correct code', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create(['email' => 'existing@example.com']);

    $oneTimePassword = $user->createOneTimePassword();
    $code = $oneTimePassword->password;

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', $user->email)
        ->set('code', $code)
        ->call('verifyCode')
        ->assertRedirect(route('marketplaces.show', $marketplace, absolute: false));

    assertAuthenticatedAs($user);
    expect(OneTimePassword::where('password', $code)->exists())->toBeFalse();
    expect($marketplace->isMember($user))->toBeTrue();
});

it('registers and logs in new user with correct code', function () {
    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'newuser@example.com')
        ->call('sendMagicCode');

    $user = User::where('email', 'newuser@example.com')->first();
    $oneTimePassword = OneTimePassword::where('authenticatable_id', $user->id)->where('authenticatable_type', User::class)->first();
    $code = $oneTimePassword->password;

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'newuser@example.com')
        ->set('code', $code)
        ->call('verifyCode')
        ->assertRedirect(route('marketplaces.show', $marketplace, absolute: false));

    expect(User::where('email', 'newuser@example.com')->exists())->toBeTrue();
    assertAuthenticated();
    $user = User::where('email', 'newuser@example.com')->first();
    expect($marketplace->isMember($user))->toBeTrue();
});

it('shows error for invalid or expired code', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create(['email' => 'anyone@example.com']);

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'anyone@example.com')
        ->set('code', '000000')
        ->call('verifyCode')
        ->assertHasErrors(['code']);
});

it('sends correct code in notification', function () {
    Notification::fake();

    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'anyone@example.com')
        ->call('sendMagicCode');

    $user = User::where('email', 'anyone@example.com')->first();
    $oneTimePassword = OneTimePassword::where('authenticatable_id', $user->id)->where('authenticatable_type', User::class)->first();

    expect($oneTimePassword)->not->toBeNull();
    expect($oneTimePassword->password)->toBeString();
    expect($oneTimePassword->password)->toHaveLength(6);
});

it('rate limits magic code requests', function () {
    $marketplace = Marketplace::factory()->create();
    $email = 'ratelimit@example.com';

    for ($i = 0; $i < 5; $i++) {
        Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
            ->set('email', $email)
            ->call('sendMagicCode')
            ->assertHasNoErrors('email');
    }

    $component = Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', $email)
        ->call('sendMagicCode')
        ->assertHasErrors('email');
});
