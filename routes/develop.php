<?php

use App\Http\Controllers\OrganizationInvitationAcceptController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', 'settings.profile')->name('settings.profile');
    Route::livewire('settings/password', 'settings.password')->name('settings.password');
    Route::livewire('settings/appearance', 'settings.appearance')->name('settings.appearance');

    Route::livewire('organizations/{organization}/settings/members', 'organizations.settings.members')
        ->name('organizations.settings.members');
    Route::livewire('organizations/{organization}/settings/general', 'organizations.settings.general')
        ->name('organizations.settings.general');
    Route::livewire('organizations/{organization}', 'organizations.settings.general')
        ->name('organizations.show');

    Route::get('organizations/invitations/{invitation}/accept', OrganizationInvitationAcceptController::class)
        ->middleware('signed')
        ->name('organizations.invitations.accept');
});

require __DIR__.'/auth.php';

require __DIR__.'/billing.php';
