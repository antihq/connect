<?php

use App\Models\MagicAuthCode;
use App\Models\Marketplace;
use App\Models\User;
use App\Notifications\MagicAuthCodeNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

use function Pest\Laravel\assertAuthenticated;
use function Pest\Laravel\assertAuthenticatedAs;

it('sends a magic code to any email', function () {
    Notification::fake();

    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'anyone@example.com')
        ->call('sendMagicCode');

    Notification::assertSentTo(
        new User(['email' => 'anyone@example.com']),
        MagicAuthCodeNotification::class
    );

    expect(MagicAuthCode::where('email', 'anyone@example.com')->exists())->toBeTrue();
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

    $code = MagicAuthCode::factory()->create([
        'email' => $user->email,
        'code' => '123456',
        'expires_at' => now()->addMinutes(10),
    ]);

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', $user->email)
        ->set('code', '123456')
        ->call('verifyCode')
        ->assertRedirect(route('marketplaces.show', $marketplace, absolute: false));

    assertAuthenticatedAs($user);
    expect(MagicAuthCode::where('code', '123456')->exists())->toBeFalse();
    expect($marketplace->isMember($user))->toBeTrue();
});

it('registers and logs in new user with correct code', function () {
    $marketplace = Marketplace::factory()->create();

    Livewire::test('pages::on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'newuser@example.com')
        ->call('sendMagicCode');

    $code = MagicAuthCode::where('email', 'newuser@example.com')->first()->code;

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

    $code = MagicAuthCode::where('email', 'anyone@example.com')->first()->code;

    Notification::assertSentTo(
        new User(['email' => 'anyone@example.com']),
        MagicAuthCodeNotification::class,
        function ($notification, $channels) use ($code) {
            return $notification->code === $code;
        }
    );
});

todo('rate limiter');
