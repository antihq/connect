<?php

use App\Http\Middleware\EnsureUserIsSubscribed;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified'])->group(function () {
    Volt::route('subscription-required', 'billing.subscription-required')->name('subscription-required');
});

Route::middleware(['auth', 'verified', EnsureUserIsSubscribed::class])->group(function () {
    Volt::route('billing-portal', 'billing.billing-portal')->name('billing.portal');
});
