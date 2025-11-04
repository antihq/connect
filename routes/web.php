<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Http\Controllers\OrganizationInvitationAcceptController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

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

require __DIR__.'/billing.php';
