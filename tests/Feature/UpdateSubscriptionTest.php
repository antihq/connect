<?php

use App\Models\UpdateSubscription;
use App\Notifications\NewUpdateSubscriberNotification;
use App\Notifications\UpdateSubscriptionConfirmationNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Livewire\Volt\Volt;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

it('subscribes with valid email and sends confirmation', function () {
    Notification::fake();

    Volt::test('welcome')
        ->set('email', 'test@example.com')
        ->call('subscribe')
        ->assertSee(__('Confirmation email sent! Please check your inbox.'));

    assertDatabaseHas('update_subscriptions', [
        'email' => 'test@example.com',
        'confirmed_at' => null,
    ]);

    Notification::assertSentTo(
        UpdateSubscription::where('email', 'test@example.com')->first(),
        UpdateSubscriptionConfirmationNotification::class
    );
});

it('confirms subscription via signed url', function () {
    Notification::fake();

    $subscription = UpdateSubscription::create(['email' => 'confirmme@example.com']);
    $url = URL::signedRoute('subscribe.confirm', ['subscription' => $subscription->id]);

    $response = get($url);
    $response->assertRedirect(route('home', ['status' => 'Your email has been confirmed!']).'#subscribe');

    $subscription = UpdateSubscription::find($subscription->id);
    expect($subscription->confirmed_at)->not->toBeNull();

    Notification::assertSentOnDemand(
        NewUpdateSubscriberNotification::class,
        function ($notification, $channels, $notifiable) use ($subscription) {
            return $notifiable->routes['mail'] === config('mail.from.address')
                && $notification->subscriberEmail === $subscription->email;
        }
    );
});

it('shows error for invalid email', function () {
    Volt::test('welcome')
        ->set('email', 'not-an-email')
        ->call('subscribe')
        ->assertHasErrors(['email']);
});

it('re-subscribes with same email if not confirmed', function () {
    Notification::fake();

    $subscription = UpdateSubscription::create(['email' => 'resend@example.com']);

    Volt::test('welcome')
        ->set('email', 'resend@example.com')
        ->call('subscribe')
        ->assertSee(__('Confirmation email sent! Please check your inbox.'));

    Notification::assertSentTo(
        $subscription,
        UpdateSubscriptionConfirmationNotification::class
    );
});
