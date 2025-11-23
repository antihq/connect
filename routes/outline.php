<?php

use App\Http\Middleware\EnsureMarketplaceMember;
use Illuminate\Support\Facades\Route;

Route::domain(config('connect.on_url'))->group(function () {
    Route::get('/', function () {
        return 'On Connect';
    })->name('marketplaces.index');

    Route::livewire('/{marketplace:slug}', 'pages::marketplaces.show')->name('marketplaces.show');

    Route::livewire('/{marketplace:slug}/search', 'pages::marketplaces.search')->name('marketplaces.search');

    Route::livewire('/{marketplace:slug}/pages/about', 'pages::marketplaces.pages.about')->name('marketplaces.pages.about');
    Route::livewire('/{marketplace:slug}/pages/terms', 'pages::marketplaces.pages.terms')->name('marketplaces.pages.terms');
    Route::livewire('/{marketplace:slug}/pages/privacy', 'pages::marketplaces.pages.privacy')->name('marketplaces.pages.privacy');

    Route::livewire('/{marketplace:slug}/inbox/orders', 'pages::marketplaces.inbox.orders')->name('marketplaces.inbox.orders');
    Route::livewire('/{marketplace:slug}/inbox/sales', 'pages::marketplaces.inbox.sales')->name('marketplaces.inbox.sales');

    Route::livewire('/{marketplace:slug}/sales', 'pages::marketplaces.sales.index')->name('marketplaces.sales.index');
    Route::livewire('/{marketplace:slug}/sales/{transaction}', 'pages::marketplaces.sales.show')->name('marketplaces.sales.show');

    Route::livewire('/{marketplace:slug}/orders', 'pages::marketplaces.orders.index')->name('marketplaces.orders.index');
    Route::livewire('/{marketplace:slug}/orders/{transaction}', 'pages::marketplaces.orders.show')->name('marketplaces.orders.show');
    Route::livewire('/{marketplace:slug}/orders/{transaction}/success', 'pages::marketplaces.orders.success')->name('marketplaces.orders.success');

    Route::livewire('/{marketplace:slug}/listings', 'pages::on-marketplace.listings.index')->name('on-marketplace.listings.index');

    Route::middleware(['auth', EnsureMarketplaceMember::class])->group(function () {
        Route::livewire('/{marketplace:slug}/listings/create', 'pages::on-marketplace.listings.create')->name('on-marketplace.listings.create');

        Route::livewire('/{marketplace:slug}/listings/{listing}/edit/details', 'pages::on-marketplace.listings.edit.details')->name('on-marketplace.listings.edit.details');

        Route::livewire('/{marketplace:slug}/listings/{listing}/edit/location', 'pages::on-marketplace.listings.edit.location')->name('on-marketplace.listings.edit.location');

        Route::livewire('/{marketplace:slug}/listings/{listing}/edit/pricing', 'pages::on-marketplace.listings.edit.pricing')->name('on-marketplace.listings.edit.pricing');

        Route::livewire('/{marketplace:slug}/listings/{listing}/edit/availability', 'pages::on-marketplace.listings.edit.availability')->name('on-marketplace.listings.edit.availability');

        Route::livewire('/{marketplace:slug}/listings/{listing}/edit/photos', 'pages::on-marketplace.listings.edit.photos')->name('on-marketplace.listings.edit.photos');
    });

    Route::middleware('auth')->group(function () {
        Route::livewire('/{marketplace:slug}/account/settings/payout', 'pages::marketplaces.account.settings.payout')->name('on-marketplace.account.settings.payout');
    });

    Route::livewire('/{marketplace:slug}/listings/{listing}', 'pages::on-marketplace.listings.show')->name('on-marketplace.listings.show');

    Route::livewire('/{marketplace:slug}/account/listings', 'pages::marketplaces.account.listings')->name('marketplaces.account.listings');

    Route::livewire('/{marketplace:slug}/account/profile', 'pages::marketplaces.profile')->name('marketplaces.profile');

    Route::livewire('/{marketplace:slug}/account/settings/contact', 'pages::marketplaces.account.settings.contact')->name('marketplaces.account.settings.contact');
    Route::livewire('/{marketplace:slug}/account/settings/password', 'pages::marketplaces.account.settings.password')->name('marketplaces.account.settings.password');
    Route::livewire('/{marketplace:slug}/account/settings/payment', 'pages::marketplaces.account.settings.payment')->name('marketplaces.account.settings.payment');

    Route::livewire('/{marketplace:slug}/users/1', 'pages::marketplaces.users.show')->name('marketplaces.users.show');

    Route::livewire('/{marketplace:slug}/sign-in', 'pages::on-marketplace.sign-in')->name('on-marketplace.sign-in');

    Route::livewire('/marketplaces/{marketplace}/transactions/{transaction}/pay', 'pages::marketplaces.transactions.pay')->name('marketplaces.transactions.pay');
    Route::livewire('/marketplaces/{marketplace}/transactions/{transaction}/pay/confirmation', 'pages::marketplaces.transactions.pay-confirmation')->name('marketplaces.transactions.pay.confirmation');
});

Route::prefix('backstage')->middleware('auth')->group(function () {
    Route::livewire('/', 'pages::backstage.home')->name('backstage.home');

    Route::livewire('/users', 'pages::backstage.users.index')->name('backstage.users.index');
    Route::livewire('/users/{user}', 'pages::backstage.users.show')->name('backstage.users.show');
    Route::livewire('/users/{user}/edit', 'pages::backstage.users.edit')->name('backstage.users.edit');

    Route::livewire('/listings', 'pages::backstage.listings.index')->name('backstage.listings.index');
    Route::livewire('/listings/{listing}', 'pages::backstage.listings.show')->name('backstage.listings.show');
    Route::livewire('/listings/{listing}/edit', 'pages::backstage.listings.edit')->name('backstage.listings.edit');

    Route::livewire('/transactions', 'pages::backstage.transactions.index')->name('backstage.transactions.index');
    Route::livewire('/transactions/{transaction}', 'pages::backstage.transactions.show')->name('backstage.transactions.show');

    Route::livewire('/reviews', 'pages::backstage.reviews.index')->middleware('auth')->name('backstage.reviews.index');
    Route::livewire('/reviews/{review}', 'pages::backstage.reviews.show')->middleware('auth')->name('backstage.reviews.show');
    Route::livewire('/reviews/{review}/edit', 'pages::backstage.reviews.edit')->name('backstage.reviews.edit');

    Route::livewire('/settings/name', 'pages::backstage.marketplaces.settings.name')->name('backstage.marketplaces.settings.name');
    Route::livewire('/settings/domain', 'pages::backstage.marketplaces.settings.domain')->name('backstage.marketplaces.settings.domain');
    Route::livewire('/settings/email', 'pages::backstage.marketplaces.settings.email')->name('backstage.marketplaces.settings.email');
    Route::livewire('/settings/localization', 'pages::backstage.marketplaces.settings.localization')->name('backstage.marketplaces.settings.localization');
    Route::livewire('/settings/access', 'pages::backstage.marketplaces.settings.access')->name('backstage.marketplaces.settings.access');

    Route::livewire('/pages', 'pages::backstage.marketplaces.pages.index')->name('backstage.marketplaces.pages.index');
    Route::livewire('/pages/create', 'pages::backstage.marketplaces.pages.create')->name('backstage.marketplaces.pages.create');
    Route::livewire('/pages/1/edit', 'pages::backstage.marketplaces.pages.edit')->name('backstage.marketplaces.pages.edit');

    Route::livewire('/settings/top-bar', 'pages::backstage.marketplaces.settings.top-bar')->name('backstage.marketplaces.settings.top-bar');
    Route::livewire('/top-bar-links/create', 'pages::backstage.marketplaces.top-bar-links.create')->name('backstage.marketplaces.top-bar-links.create');
    Route::livewire('/top-bar-links/1/edit', 'pages::backstage.marketplaces.top-bar-links.edit')->name('backstage.marketplaces.top-bar-links.edit');

    Route::livewire('/settings/footer', 'pages::backstage.marketplaces.settings.footer')->name('backstage.marketplaces.settings.footer');
    Route::livewire('/social-media-links/create', 'pages::backstage.marketplaces.social-media-links.create')->name('backstage.marketplaces.social-media-links.create');
    Route::livewire('/social-media-links/1/edit', 'pages::backstage.marketplaces.social-media-links.edit')->name('backstage.marketplaces.social-media-links.edit');
    Route::livewire('/content-blocks/create', 'pages::backstage.marketplaces.content-blocks.create')->name('backstage.marketplaces.content-blocks.create');
    Route::livewire('/content-blocks/1/edit', 'pages::backstage.marketplaces.content-blocks.edit')->name('backstage.marketplaces.content-blocks.edit');

    Route::livewire('/settings/texts', 'pages::backstage.marketplaces.settings.texts')->name('backstage.marketplaces.settings.texts');

    Route::livewire('/settings/design/branding', 'pages::backstage.marketplaces.settings.design.branding')->name('backstage.marketplaces.settings.design.branding');
    Route::livewire('/settings/design/layout', 'pages::backstage.marketplaces.settings.design.layout')->name('backstage.marketplaces.settings.design.layout');

    Route::livewire('/user-types', 'pages::backstage.marketplaces.user-types')->name('backstage.marketplaces.user-types');
    Route::livewire('/user-types/create', 'pages::backstage.marketplaces.user-types.create')->name('backstage.marketplaces.user-types.create');
    Route::livewire('/user-types/1/edit', 'pages::backstage.marketplaces.user-types.edit')->name('backstage.marketplaces.user-types.edit');

    Route::livewire('/user-fields', 'pages::backstage.marketplaces.user-fields')->name('backstage.marketplaces.user-fields');
    Route::livewire('/user-fields/create', 'pages::backstage.marketplaces.user-fields.create')->name('backstage.marketplaces.user-fields.create');
    Route::livewire('/user-fields/1/edit', 'pages::backstage.marketplaces.user-fields.edit')->name('backstage.marketplaces.user-fields.edit');

    Route::livewire('/listing-types', 'pages::backstage.marketplaces.listing-types')->name('backstage.marketplaces.listing-types');
    Route::livewire('/listing-types/create', 'pages::backstage.marketplaces.listing-types.create')->name('backstage.marketplaces.listing-types.create');
    Route::livewire('/listing-types/1/edit', 'pages::backstage.marketplaces.listing-types.edit')->name('backstage.marketplaces.listing-types.edit');

    Route::livewire('/listing-categories', 'pages::backstage.marketplaces.listing-categories')->name('backstage.marketplaces.listing-categories');
    Route::livewire('/listing-categories/create', 'pages::backstage.marketplaces.listing-categories.create')->name('backstage.marketplaces.listing-categories.create');
    Route::livewire('/listing-categories/1/edit', 'pages::backstage.marketplaces.listing-categories.edit')->name('backstage.marketplaces.listing-categories.edit');

    Route::livewire('/listing-fields', 'pages::backstage.marketplaces.listing-fields')->name('backstage.marketplaces.listing-fields');
    Route::livewire('/listing-fields/create', 'pages::backstage.marketplaces.listing-fields.create')->name('backstage.marketplaces.listing-fields.create');
    Route::livewire('/listing-fields/1/edit', 'pages::backstage.marketplaces.listing-fields.edit')->name('backstage.marketplaces.listing-fields.edit');

    Route::livewire('/settings/search', 'pages::backstage.marketplaces.settings.search')->name('backstage.marketplaces.settings.search');
    Route::livewire('/settings/transaction', 'pages::backstage.marketplaces.settings.transaction')->name('backstage.marketplaces.settings.transaction');
    Route::livewire('/settings/commission', 'pages::backstage.marketplaces.settings.commission')->name('backstage.marketplaces.settings.commission');

    Route::livewire('/settings/paymenents', 'pages::backstage.marketplaces.settings.payments')->name('backstage.marketplaces.settings.payments');
    Route::livewire('/settings/maps', 'pages::backstage.marketplaces.settings.maps')->name('backstage.marketplaces.settings.maps');
    Route::livewire('/settings/analytics', 'pages::backstage.marketplaces.settings.analytics')->name('backstage.marketplaces.settings.analytics');
    Route::livewire('/settings/google', 'pages::backstage.marketplaces.settings.google')->name('backstage.marketplaces.settings.google');
    Route::livewire('/settings/zapier', 'pages::backstage.marketplaces.settings.zapier')->name('backstage.marketplaces.settings.zapier');
});
