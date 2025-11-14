<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::domain(config('connect.on_url'))->group(function () {
    Route::get('/', function () {
        return 'On Connect';
    })->name('marketplaces.index');

    Volt::route('/{marketplace:slug}', 'marketplaces.show')->name('marketplaces.show');

    Volt::route('/{marketplace:slug}/search', 'marketplaces.search')->name('marketplaces.search');

    Volt::route('/{marketplace:slug}/pages/about', 'marketplaces.pages.about')->name('marketplaces.pages.about');
    Volt::route('/{marketplace:slug}/pages/terms', 'marketplaces.pages.terms')->name('marketplaces.pages.terms');
    Volt::route('/{marketplace:slug}/pages/privacy', 'marketplaces.pages.privacy')->name('marketplaces.pages.privacy');

    Volt::route('/{marketplace:slug}/inbox/orders', 'marketplaces.inbox.orders')->name('marketplaces.inbox.orders');
    Volt::route('/{marketplace:slug}/inbox/sales', 'marketplaces.inbox.sales')->name('marketplaces.inbox.sales');

    Volt::route('/{marketplace:slug}/sales', 'marketplaces.sales.index')->name('marketplaces.sales.index');
    Volt::route('/{marketplace:slug}/sales/{transaction}', 'marketplaces.sales.show')->name('marketplaces.sales.show');

    Volt::route('/{marketplace:slug}/orders', 'marketplaces.orders.index')->name('marketplaces.orders.index');
    Volt::route('/{marketplace:slug}/orders/{transaction}', 'marketplaces.orders.show')->name('marketplaces.orders.show');

    Volt::route('/{marketplace:slug}/listings', 'on-marketplace.listings.index')->name('on-marketplace.listings.index');

    Route::middleware('auth')->group(function () {
        Volt::route('/{marketplace:slug}/listings/create', 'on-marketplace.listings.create')->name('on-marketplace.listings.create');

        Volt::route('/{marketplace:slug}/listings/{listing}/edit/details', 'on-marketplace.listings.edit.details')->name('on-marketplace.listings.edit.details');
        Volt::route('/{marketplace:slug}/listings/{listing}/edit/location', 'on-marketplace.listings.edit.location')->name('on-marketplace.listings.edit.location');
        Volt::route('/{marketplace:slug}/listings/{listing}/edit/pricing', 'on-marketplace.listings.edit.pricing')->name('on-marketplace.listings.edit.pricing');
        Volt::route('/{marketplace:slug}/listings/{listing}/edit/availability', 'on-marketplace.listings.edit.availability')->name('on-marketplace.listings.edit.availability');
        Volt::route('/{marketplace:slug}/listings/{listing}/edit/photos', 'on-marketplace.listings.edit.photos')->name('on-marketplace.listings.edit.photos');

        Volt::route('/{marketplace:slug}/account/settings/payout', 'marketplaces.account.settings.payout')->name('marketplaces.account.settings.payout');
    });

    Volt::route('/{marketplace:slug}/listings/{listing}', 'on-marketplace.listings.show')->name('on-marketplace.listings.show');

    Volt::route('/{marketplace:slug}/account/listings', 'marketplaces.account.listings')->name('marketplaces.account.listings');

    Volt::route('/{marketplace:slug}/account/profile', 'marketplaces.profile')->name('marketplaces.profile');

    Volt::route('/{marketplace:slug}/account/settings/contact', 'marketplaces.account.settings.contact')->name('marketplaces.account.settings.contact');
    Volt::route('/{marketplace:slug}/account/settings/password', 'marketplaces.account.settings.password')->name('marketplaces.account.settings.password');
    Volt::route('/{marketplace:slug}/account/settings/payment', 'marketplaces.account.settings.payment')->name('marketplaces.account.settings.payment');

    Volt::route('/{marketplace:slug}/users/1', 'marketplaces.users.show')->name('marketplaces.users.show');

    Volt::route('/{marketplace:slug}/sign-in', 'on-marketplace.sign-in')->name('on-marketplace.sign-in')->middleware('guest');

    Volt::route('/marketplaces/{marketplace}/transactions/{transaction}/pay', 'marketplaces.transactions.pay')->name('marketplaces.transactions.pay');
    Volt::route('/marketplaces/{marketplace}/transactions/{transaction}/pay/confirmation', 'marketplaces.transactions.pay-confirmation')->name('marketplaces.transactions.pay.confirmation');
});

Route::prefix('backstage')->middleware('auth')->group(function () {
    Volt::route('/', 'backstage.home')->name('backstage.home');

    Volt::route('/users', 'backstage.users.index')->name('backstage.users.index');
    Volt::route('/users/{user}', 'backstage.users.show')->name('backstage.users.show');
    Volt::route('/users/{user}/edit', 'backstage.users.edit')->name('backstage.users.edit');

    Volt::route('/listings', 'backstage.listings.index')->name('backstage.listings.index');
    Volt::route('/listings/{listing}', 'backstage.listings.show')->name('backstage.listings.show');
    Volt::route('/listings/{listing}/edit', 'backstage.listings.edit')->name('backstage.listings.edit');

    Volt::route('/transactions', 'backstage.transactions.index')->name('backstage.transactions.index');
    Volt::route('/transactions/{transaction}', 'backstage.transactions.show')->name('backstage.transactions.show');

    Volt::route('/reviews', 'backstage.reviews.index')->middleware('auth')->name('backstage.reviews.index');
    Volt::route('/reviews/{review}', 'backstage.reviews.show')->middleware('auth')->name('backstage.reviews.show');
    Volt::route('/reviews/{review}/edit', 'backstage.reviews.edit')->name('backstage.reviews.edit');

    Volt::route('/settings/name', 'backstage.marketplaces.settings.name')->name('backstage.marketplaces.settings.name');
    Volt::route('/settings/domain', 'backstage.marketplaces.settings.domain')->name('backstage.marketplaces.settings.domain');
    Volt::route('/settings/email', 'backstage.marketplaces.settings.email')->name('backstage.marketplaces.settings.email');
    Volt::route('/settings/localization', 'backstage.marketplaces.settings.localization')->name('backstage.marketplaces.settings.localization');
    Volt::route('/settings/access', 'backstage.marketplaces.settings.access')->name('backstage.marketplaces.settings.access');

    Volt::route('/pages', 'backstage.marketplaces.pages.index')->name('backstage.marketplaces.pages.index');
    Volt::route('/pages/create', 'backstage.marketplaces.pages.create')->name('backstage.marketplaces.pages.create');
    Volt::route('/pages/1/edit', 'backstage.marketplaces.pages.edit')->name('backstage.marketplaces.pages.edit');

    Volt::route('/settings/top-bar', 'backstage.marketplaces.settings.top-bar')->name('backstage.marketplaces.settings.top-bar');
    Volt::route('/top-bar-links/create', 'backstage.marketplaces.top-bar-links.create')->name('backstage.marketplaces.top-bar-links.create');
    Volt::route('/top-bar-links/1/edit', 'backstage.marketplaces.top-bar-links.edit')->name('backstage.marketplaces.top-bar-links.edit');

    Volt::route('/settings/footer', 'backstage.marketplaces.settings.footer')->name('backstage.marketplaces.settings.footer');
    Volt::route('/social-media-links/create', 'backstage.marketplaces.social-media-links.create')->name('backstage.marketplaces.social-media-links.create');
    Volt::route('/social-media-links/1/edit', 'backstage.marketplaces.social-media-links.edit')->name('backstage.marketplaces.social-media-links.edit');
    Volt::route('/content-blocks/create', 'backstage.marketplaces.content-blocks.create')->name('backstage.marketplaces.content-blocks.create');
    Volt::route('/content-blocks/1/edit', 'backstage.marketplaces.content-blocks.edit')->name('backstage.marketplaces.content-blocks.edit');

    Volt::route('/settings/texts', 'backstage.marketplaces.settings.texts')->name('backstage.marketplaces.settings.texts');

    Volt::route('/settings/design/branding', 'backstage.marketplaces.settings.design.branding')->name('backstage.marketplaces.settings.design.branding');
    Volt::route('/settings/design/layout', 'backstage.marketplaces.settings.design.layout')->name('backstage.marketplaces.settings.design.layout');

    Volt::route('/user-types', 'backstage.marketplaces.user-types')->name('backstage.marketplaces.user-types');
    Volt::route('/user-types/create', 'backstage.marketplaces.user-types.create')->name('backstage.marketplaces.user-types.create');
    Volt::route('/user-types/1/edit', 'backstage.marketplaces.user-types.edit')->name('backstage.marketplaces.user-types.edit');

    Volt::route('/user-fields', 'backstage.marketplaces.user-fields')->name('backstage.marketplaces.user-fields');
    Volt::route('/user-fields/create', 'backstage.marketplaces.user-fields.create')->name('backstage.marketplaces.user-fields.create');
    Volt::route('/user-fields/1/edit', 'backstage.marketplaces.user-fields.edit')->name('backstage.marketplaces.user-fields.edit');

    Volt::route('/listing-types', 'backstage.marketplaces.listing-types')->name('backstage.marketplaces.listing-types');
    Volt::route('/listing-types/create', 'backstage.marketplaces.listing-types.create')->name('backstage.marketplaces.listing-types.create');
    Volt::route('/listing-types/1/edit', 'backstage.marketplaces.listing-types.edit')->name('backstage.marketplaces.listing-types.edit');

    Volt::route('/listing-categories', 'backstage.marketplaces.listing-categories')->name('backstage.marketplaces.listing-categories');
    Volt::route('/listing-categories/create', 'backstage.marketplaces.listing-categories.create')->name('backstage.marketplaces.listing-categories.create');
    Volt::route('/listing-categories/1/edit', 'backstage.marketplaces.listing-categories.edit')->name('backstage.marketplaces.listing-categories.edit');

    Volt::route('/listing-fields', 'backstage.marketplaces.listing-fields')->name('backstage.marketplaces.listing-fields');
    Volt::route('/listing-fields/create', 'backstage.marketplaces.listing-fields.create')->name('backstage.marketplaces.listing-fields.create');
    Volt::route('/listing-fields/1/edit', 'backstage.marketplaces.listing-fields.edit')->name('backstage.marketplaces.listing-fields.edit');

    Volt::route('/settings/search', 'backstage.marketplaces.settings.search')->name('backstage.marketplaces.settings.search');
    Volt::route('/settings/transaction', 'backstage.marketplaces.settings.transaction')->name('backstage.marketplaces.settings.transaction');
    Volt::route('/settings/commission', 'backstage.marketplaces.settings.commission')->name('backstage.marketplaces.settings.commission');

    Volt::route('/settings/paymenents', 'backstage.marketplaces.settings.payments')->name('backstage.marketplaces.settings.payments');
    Volt::route('/settings/maps', 'backstage.marketplaces.settings.maps')->name('backstage.marketplaces.settings.maps');
    Volt::route('/settings/analytics', 'backstage.marketplaces.settings.analytics')->name('backstage.marketplaces.settings.analytics');
    Volt::route('/settings/google', 'backstage.marketplaces.settings.google')->name('backstage.marketplaces.settings.google');
    Volt::route('/settings/zapier', 'backstage.marketplaces.settings.zapier')->name('backstage.marketplaces.settings.zapier');
});
