<?php

use Livewire\Volt\Volt;

Volt::route('/marketplaces/{marketplace}/transactions/{transaction}/pay', 'marketplaces.transactions.pay')->name('marketplaces.transactions.pay');
Volt::route('/marketplaces/{marketplace}/transactions/{transaction}/pay/confirmation', 'marketplaces.transactions.pay-confirmation')->name('marketplaces.transactions.pay.confirmation');

Volt::route('/marketplace/{marketplace}/', 'marketplaces.show')->name('marketplaces.show');
Volt::route('/marketplace/{marketplace}/search', 'marketplaces.search')->name('marketplaces.search');

Volt::route('/marketplace/{marketplace}/pages/about', 'marketplaces.pages.about')->name('marketplaces.pages.about');
Volt::route('/marketplace/{marketplace}/pages/terms', 'marketplaces.pages.terms')->name('marketplaces.pages.terms');
Volt::route('/marketplace/{marketplace}/pages/privacy', 'marketplaces.pages.privacy')->name('marketplaces.pages.privacy');

Volt::route('/marketplace/{marketplace}/inbox/orders', 'marketplaces.inbox.orders')->name('marketplaces.inbox.orders');
Volt::route('/marketplace/{marketplace}/inbox/sales', 'marketplaces.inbox.sales')->name('marketplaces.inbox.sales');

Volt::route('/marketplace/{marketplace}/sales', 'marketplaces.sales.index')->name('marketplaces.sales.index');
Volt::route('/marketplace/{marketplace}/sales/{transaction}', 'marketplaces.sales.show')->name('marketplaces.sales.show');

Volt::route('/marketplace/{marketplace}/orders', 'marketplaces.orders.index')->name('marketplaces.orders.index');
Volt::route('/marketplace/{marketplace}/orders/{transaction}', 'marketplaces.orders.show')->name('marketplaces.orders.show');

Volt::route('/marketplace/{marketplace}/listings', 'marketplaces.listings.index')->name('marketplaces.listings.index');
Volt::route('/marketplace/{marketplace}/listings/create', 'marketplaces.listings.create')->name('marketplaces.listings.create');
Volt::route('/marketplace/{marketplace}/listings/{listing}', 'marketplaces.listings.show')->name('marketplaces.listings.show');

Volt::route('/marketplace/{marketplace}/account/listings', 'marketplaces.account.listings')->name('marketplaces.account.listings');
Volt::route('/marketplace/{marketplace}/listings/{listing}/edit/details', 'marketplaces.listings.edit.details')->name('marketplaces.listings.edit.details');
Volt::route('/marketplace/{marketplace}/listings/{listing}/edit/location', 'marketplaces.listings.edit.location')->name('marketplaces.listings.edit.location');
Volt::route('/marketplace/{marketplace}/listings/{listing}/edit/pricing', 'marketplaces.listings.edit.pricing')->name('marketplaces.listings.edit.pricing');
Volt::route('/marketplace/{marketplace}/listings/{listing}/edit/availability', 'marketplaces.listings.edit.availability')->name('marketplaces.listings.edit.availability');
Volt::route('/marketplace/{marketplace}/listings/{listing}/edit/photos', 'marketplaces.listings.edit.photos')->name('marketplaces.listings.edit.photos');

Volt::route('/marketplace/{marketplace}/account/profile', 'marketplaces.profile')->name('marketplaces.profile');

Volt::route('/marketplace/{marketplace}/account/settings/contact', 'marketplaces.account.settings.contact')->name('marketplaces.account.settings.contact');
Volt::route('/marketplace/{marketplace}/account/settings/password', 'marketplaces.account.settings.password')->name('marketplaces.account.settings.password');
Volt::route('/marketplace/{marketplace}/account/settings/payout', 'marketplaces.account.settings.payout')->name('marketplaces.account.settings.payout');
Volt::route('/marketplace/{marketplace}/account/settings/payment', 'marketplaces.account.settings.payment')->name('marketplaces.account.settings.payment');

Volt::route('/marketplace/{marketplace}/users/1', 'marketplaces.users.show')->name('marketplaces.users.show');

Volt::route('/marketplace/{marketplace}/login', 'marketplaces.login')->name('marketplaces.login');
Volt::route('/marketplace/{marketplace}/register', 'marketplaces.register')->name('marketplaces.register');

Volt::route('/backstage', 'backstage.home')->name('backstage.home');

Volt::route('/backstage/users', 'backstage.users.index')->name('backstage.users.index');
Volt::route('/backstage/users/{user}', 'backstage.users.show')->name('backstage.users.show');
Volt::route('/backstage/users/{user}/edit', 'backstage.users.edit')->name('backstage.users.edit');

Volt::route('/backstage/listings', 'backstage.listings.index')->name('backstage.listings.index');
Volt::route('/backstage/listings/1', 'backstage.listings.show')->name('backstage.listings.show');
Volt::route('/backstage/listings/1/edit', 'backstage.listings.edit')->name('backstage.listings.edit');

Volt::route('/backstage/transactions', 'backstage.transactions.index')->name('backstage.transactions.index');
Volt::route('/backstage/transactions/1', 'backstage.transactions.show')->name('backstage.transactions.show');

Volt::route('/backstage/reviews', 'backstage.reviews.index')->name('backstage.reviews.index');
Volt::route('/backstage/reviews/1', 'backstage.reviews.show')->name('backstage.reviews.show');
Volt::route('/backstage/reviews/1/edit', 'backstage.reviews.edit')->name('backstage.reviews.edit');

Volt::route('/backstage/marketplaces/{marketplace}/settings/name', 'backstage.marketplaces.settings.name')->name('backstage.marketplaces.settings.name');
Volt::route('/backstage/marketplaces/{marketplace}/settings/domain', 'backstage.marketplaces.settings.domain')->name('backstage.marketplaces.settings.domain');
Volt::route('/backstage/marketplaces/{marketplace}/settings/domain', 'backstage.marketplaces.settings.domain')->name('backstage.marketplaces.settings.domain');
Volt::route('/backstage/marketplaces/{marketplace}/settings/email', 'backstage.marketplaces.settings.email')->name('backstage.marketplaces.settings.email');
Volt::route('/backstage/marketplaces/{marketplace}/settings/localization', 'backstage.marketplaces.settings.localization')->name('backstage.marketplaces.settings.localization');
Volt::route('/backstage/marketplaces/{marketplace}/settings/access', 'backstage.marketplaces.settings.access')->name('backstage.marketplaces.settings.access');

Volt::route('/backstage/marketplaces/{marketplace}/pages', 'backstage.marketplaces.pages.index')->name('backstage.marketplaces.pages.index');
Volt::route('/backstage/marketplaces/{marketplace}/pages/create', 'backstage.marketplaces.pages.create')->name('backstage.marketplaces.pages.create');
Volt::route('/backstage/marketplaces/{marketplace}/pages/1/edit', 'backstage.marketplaces.pages.edit')->name('backstage.marketplaces.pages.edit');

Volt::route('/backstage/marketplaces/{marketplace}/settings/top-bar', 'backstage.marketplaces.settings.top-bar')->name('backstage.marketplaces.settings.top-bar');
Volt::route('/backstage/marketplaces/{marketplace}/top-bar-links/create', 'backstage.marketplaces.top-bar-links.create')->name('backstage.marketplaces.top-bar-links.create');
Volt::route('/backstage/marketplaces/{marketplace}/top-bar-links/1/edit', 'backstage.marketplaces.top-bar-links.edit')->name('backstage.marketplaces.top-bar-links.edit');

Volt::route('/backstage/marketplaces/{marketplace}/settings/footer', 'backstage.marketplaces.settings.footer')->name('backstage.marketplaces.settings.footer');
Volt::route('/backstage/marketplaces/{marketplace}/social-media-links/create', 'backstage.marketplaces.social-media-links.create')->name('backstage.marketplaces.social-media-links.create');
Volt::route('/backstage/marketplaces/{marketplace}/social-media-links/1/edit', 'backstage.marketplaces.social-media-links.edit')->name('backstage.marketplaces.social-media-links.edit');
Volt::route('/backstage/marketplaces/{marketplace}/content-blocks/create', 'backstage.marketplaces.content-blocks.create')->name('backstage.marketplaces.content-blocks.create');
Volt::route('/backstage/marketplaces/{marketplace}/content-blocks/1/edit', 'backstage.marketplaces.content-blocks.edit')->name('backstage.marketplaces.content-blocks.edit');

Volt::route('/backstage/marketplaces/{marketplace}/settings/texts', 'backstage.marketplaces.settings.texts')->name('backstage.marketplaces.settings.texts');

Volt::route('/backstage/marketplaces/{marketplace}/settings/design/branding', 'backstage.marketplaces.settings.design.branding')->name('backstage.marketplaces.settings.design.branding');
Volt::route('/backstage/marketplaces/{marketplace}/settings/design/layout', 'backstage.marketplaces.settings.design.layout')->name('backstage.marketplaces.settings.design.layout');

Volt::route('/backstage/marketplaces/{marketplace}/user-types', 'backstage.marketplaces.user-types')->name('backstage.marketplaces.user-types');
Volt::route('/backstage/marketplaces/{marketplace}/user-types/create', 'backstage.marketplaces.user-types.create')->name('backstage.marketplaces.user-types.create');
Volt::route('/backstage/marketplaces/{marketplace}/user-types/1/edit', 'backstage.marketplaces.user-types.edit')->name('backstage.marketplaces.user-types.edit');

Volt::route('/backstage/marketplaces/{marketplace}/user-fields', 'backstage.marketplaces.user-fields')->name('backstage.marketplaces.user-fields');
Volt::route('/backstage/marketplaces/{marketplace}/user-fields/create', 'backstage.marketplaces.user-fields.create')->name('backstage.marketplaces.user-fields.create');
Volt::route('/backstage/marketplaces/{marketplace}/user-fields/1/edit', 'backstage.marketplaces.user-fields.edit')->name('backstage.marketplaces.user-fields.edit');

Volt::route('/backstage/marketplaces/{marketplace}/listing-types', 'backstage.marketplaces.listing-types')->name('backstage.marketplaces.listing-types');
Volt::route('/backstage/marketplaces/{marketplace}/listing-types/create', 'backstage.marketplaces.listing-types.create')->name('backstage.marketplaces.listing-types.create');
Volt::route('/backstage/marketplaces/{marketplace}/listing-types/1/edit', 'backstage.marketplaces.listing-types.edit')->name('backstage.marketplaces.listing-types.edit');

Volt::route('/backstage/marketplaces/{marketplace}/listing-categories', 'backstage.marketplaces.listing-categories')->name('backstage.marketplaces.listing-categories');
Volt::route('/backstage/marketplaces/{marketplace}/listing-categories/create', 'backstage.marketplaces.listing-categories.create')->name('backstage.marketplaces.listing-categories.create');
Volt::route('/backstage/marketplaces/{marketplace}/listing-categories/1/edit', 'backstage.marketplaces.listing-categories.edit')->name('backstage.marketplaces.listing-categories.edit');

Volt::route('/backstage/marketplaces/{marketplace}/listing-fields', 'backstage.marketplaces.listing-fields')->name('backstage.marketplaces.listing-fields');
Volt::route('/backstage/marketplaces/{marketplace}/listing-fields/create', 'backstage.marketplaces.listing-fields.create')->name('backstage.marketplaces.listing-fields.create');
Volt::route('/backstage/marketplaces/{marketplace}/listing-fields/1/edit', 'backstage.marketplaces.listing-fields.edit')->name('backstage.marketplaces.listing-fields.edit');

Volt::route('/backstage/marketplaces/{marketplace}/settings/search', 'backstage.marketplaces.settings.search')->name('backstage.marketplaces.settings.search');
Volt::route('/backstage/marketplaces/{marketplace}/settings/transaction', 'backstage.marketplaces.settings.transaction')->name('backstage.marketplaces.settings.transaction');
Volt::route('/backstage/marketplaces/{marketplace}/settings/commission', 'backstage.marketplaces.settings.commission')->name('backstage.marketplaces.settings.commission');

Volt::route('/backstage/marketplaces/{marketplace}/settings/paymenents', 'backstage.marketplaces.settings.payments')->name('backstage.marketplaces.settings.payments');
Volt::route('/backstage/marketplaces/{marketplace}/settings/maps', 'backstage.marketplaces.settings.maps')->name('backstage.marketplaces.settings.maps');
Volt::route('/backstage/marketplaces/{marketplace}/settings/analytics', 'backstage.marketplaces.settings.analytics')->name('backstage.marketplaces.settings.analytics');
Volt::route('/backstage/marketplaces/{marketplace}/settings/google', 'backstage.marketplaces.settings.google')->name('backstage.marketplaces.settings.google');
Volt::route('/backstage/marketplaces/{marketplace}/settings/zapier', 'backstage.marketplaces.settings.zapier')->name('backstage.marketplaces.settings.zapier');
