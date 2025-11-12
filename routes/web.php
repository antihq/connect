<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use App\Models\UpdateSubscription;
use App\Notifications\NewUpdateSubscriberNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Volt::route('/', 'welcome')->name('home');
Volt::route('/changelog', 'changelog')->name('changelog');

Volt::route('/vote', 'vote');

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::get('subscribe/confirm/{subscription}', function (UpdateSubscription $subscription) {
    if ($subscription->confirmed_at) {
        return redirect()->to(route('home', ['status' => 'Your email is already confirmed.']).'#subscribe');
    }

    $subscription->update(['confirmed_at' => now()]);

    Notification::route('mail', config('mail.from.address'))
        ->notify(new NewUpdateSubscriberNotification($subscription->email));

    return redirect()->to(route('home', ['status' => 'Your email has been confirmed!']).'#subscribe');
})->middleware('signed')->name('subscribe.confirm');

if (!app()->isProduction()) {
    require __DIR__.'/outline.php';

    require __DIR__.'/develop.php';
}
