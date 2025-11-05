<?php

use App\Http\Controllers\OrganizationInvitationAcceptController;
use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Models\UpdateSubscription;
use App\Notifications\NewUpdateSubscriberNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Volt::route('organizations/{organization}/settings/members', 'organizations.settings.members')
        ->name('organizations.settings.members');
    Volt::route('organizations/{organization}/settings/general', 'organizations.settings.general')
        ->name('organizations.settings.general');
    Volt::route('organizations/{organization}', 'organizations.settings.general')
        ->name('organizations.show');

    Route::get('organizations/invitations/{invitation}/accept', OrganizationInvitationAcceptController::class)
        ->middleware('signed')
        ->name('organizations.invitations.accept');
});

require __DIR__.'/auth.php';

Route::get('subscribe/confirm/{subscription}', function (UpdateSubscription $subscription) {
    if ($subscription->confirmed_at) {
        return redirect()->to(route('home', ['status' => 'Your email is already confirmed.']).'#subscribe');
    }

    $subscription->update(['confirmed_at' => now()]);

    Notification::route('mail', config('mail.from.address'))
        ->notify(new NewUpdateSubscriberNotification($subscription->email));

    return redirect()->to(route('home', ['status' => 'Your email has been confirmed!']).'#subscribe');
})->middleware('signed')->name('subscribe.confirm');

require __DIR__.'/billing.php';
