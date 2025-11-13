<?php

use App\Models\MagicAuthCode;
use App\Models\Marketplace;
use App\Models\User;
use App\Notifications\MagicAuthCodeNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Volt\Volt;

it('sends a magic code to any email', function () {
    Notification::fake();

    $marketplace = Marketplace::factory()->create();

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
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

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'not-an-email')
        ->call('sendMagicCode')
        ->assertHasErrors(['email']);
});

it('logs in existing user with correct code', function () {
    $marketplace = Marketplace::factory()->create();
    $user = User::factory()->create(['email' => 'existing@example.com']);

    $code = MagicAuthCode::create([
        'email' => $user->email,
        'code' => '123456',
        'expires_at' => now()->addMinutes(10),
    ]);

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', $user->email)
        ->set('code', '123456')
        ->call('verifyCode')
        ->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);
    expect(MagicAuthCode::where('code', '123456')->exists())->toBeFalse();
    $this->assertDatabaseHas('organization_user', [
        'organization_id' => $marketplace->organization->id,
        'user_id' => $user->id,
        'role' => 'member',
    ]);

});

it('registers and logs in new user with correct code', function () {
    $marketplace = Marketplace::factory()->create();

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'newuser@example.com')
        ->call('sendMagicCode');

    $code = MagicAuthCode::where('email', 'newuser@example.com')->first()->code;

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'newuser@example.com')
        ->set('code', $code)
        ->call('verifyCode')
        ->assertRedirect('/dashboard');

    $this->assertDatabaseHas('users', ['email' => 'newuser@example.com']);
    $this->assertAuthenticated();
    $user = User::where('email', 'newuser@example.com')->first();
    $this->assertDatabaseHas('organization_user', [
        'organization_id' => $marketplace->organization->id,
        'user_id' => $user->id,
        'role' => 'member',
    ]);
});

it('shows error for invalid or expired code', function () {
    $marketplace = Marketplace::factory()->create();

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
        ->set('email', 'anyone@example.com')
        ->set('code', '000000')
        ->call('verifyCode')
        ->assertSee('Invalid or expired code');
});

it('sends correct code in notification', function () {
    Notification::fake();

    $marketplace = Marketplace::factory()->create();

    Volt::test('on-marketplace.sign-in', ['marketplace' => $marketplace])
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
