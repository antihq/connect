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
Volt::route('/marketplace/{marketplace}/sales/1', 'marketplaces.sales.show')->name('marketplaces.sales.show');

Volt::route('/marketplace/{marketplace}/orders', 'marketplaces.orders.index')->name('marketplaces.orders.index');
Volt::route('/marketplace/{marketplace}/orders/1', 'marketplaces.orders.show')->name('marketplaces.orders.show');

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

Volt::route('/cp', 'cp')->name('/cp');

Volt::route('/cp/users', 'cp.users.index')->name('cp.users.index');
Volt::route('/cp/users/1', 'cp.users.show')->name('cp.users.show');
Volt::route('/cp/users/1/edit', 'cp.users.edit')->name('cp.users.edit');

Volt::route('/cp/listings', 'cp.listings.index')->name('cp.listings.index');
Volt::route('/cp/listings/1', 'cp.listings.show')->name('cp.listings.show');
Volt::route('/cp/listings/1/edit', 'cp.listings.edit')->name('cp.listings.edit');

Volt::route('/cp/transactions', 'cp.transactions.index')->name('cp.transactions.index');
Volt::route('/cp/transactions/1', 'cp.transactions.show')->name('cp.transactions.show');

Volt::route('/cp/reviews', 'cp.reviews.index')->name('cp.reviews.index');
Volt::route('/cp/reviews/1', 'cp.reviews.show')->name('cp.reviews.show');
Volt::route('/cp/reviews/1/edit', 'cp.reviews.edit')->name('cp.reviews.edit');

Volt::route('/cp/marketplaces/{marketplace}/settings/name', 'cp.marketplaces.settings.name')->name('cp.marketplaces.settings.name');
Volt::route('/cp/marketplaces/{marketplace}/settings/domain', 'cp.marketplaces.settings.domain')->name('cp.marketplaces.settings.domain');
Volt::route('/cp/marketplaces/{marketplace}/settings/domain', 'cp.marketplaces.settings.domain')->name('cp.marketplaces.settings.domain');
Volt::route('/cp/marketplaces/{marketplace}/settings/email', 'cp.marketplaces.settings.email')->name('cp.marketplaces.settings.email');
Volt::route('/cp/marketplaces/{marketplace}/settings/localization', 'cp.marketplaces.settings.localization')->name('cp.marketplaces.settings.localization');
Volt::route('/cp/marketplaces/{marketplace}/settings/access', 'cp.marketplaces.settings.access')->name('cp.marketplaces.settings.access');

Volt::route('/cp/marketplaces/{marketplace}/pages', 'cp.marketplaces.pages.index')->name('cp.marketplaces.pages.index');
Volt::route('/cp/marketplaces/{marketplace}/pages/create', 'cp.marketplaces.pages.create')->name('cp.marketplaces.pages.create');
Volt::route('/cp/marketplaces/{marketplace}/pages/1/edit', 'cp.marketplaces.pages.edit')->name('cp.marketplaces.pages.edit');

Volt::route('/cp/marketplaces/{marketplace}/settings/top-bar', 'cp.marketplaces.settings.top-bar')->name('cp.marketplaces.settings.top-bar');
Volt::route('/cp/marketplaces/{marketplace}/top-bar-links/create', 'cp.marketplaces.top-bar-links.create')->name('cp.marketplaces.top-bar-links.create');
Volt::route('/cp/marketplaces/{marketplace}/top-bar-links/1/edit', 'cp.marketplaces.top-bar-links.edit')->name('cp.marketplaces.top-bar-links.edit');

Volt::route('/cp/marketplaces/{marketplace}/settings/footer', 'cp.marketplaces.settings.footer')->name('cp.marketplaces.settings.footer');
Volt::route('/cp/marketplaces/{marketplace}/social-media-links/create', 'cp.marketplaces.social-media-links.create')->name('cp.marketplaces.social-media-links.create');
Volt::route('/cp/marketplaces/{marketplace}/social-media-links/1/edit', 'cp.marketplaces.social-media-links.edit')->name('cp.marketplaces.social-media-links.edit');
Volt::route('/cp/marketplaces/{marketplace}/content-blocks/create', 'cp.marketplaces.content-blocks.create')->name('cp.marketplaces.content-blocks.create');
Volt::route('/cp/marketplaces/{marketplace}/content-blocks/1/edit', 'cp.marketplaces.content-blocks.edit')->name('cp.marketplaces.content-blocks.edit');

Volt::route('/cp/marketplaces/{marketplace}/settings/texts', 'cp.marketplaces.settings.texts')->name('cp.marketplaces.settings.texts');

Volt::route('/cp/marketplaces/{marketplace}/settings/design/branding', 'cp.marketplaces.settings.design.branding')->name('cp.marketplaces.settings.design.branding');
Volt::route('/cp/marketplaces/{marketplace}/settings/design/layout', 'cp.marketplaces.settings.design.layout')->name('cp.marketplaces.settings.design.layout');

Volt::route('/cp/marketplaces/{marketplace}/user-types', 'cp.marketplaces.user-types')->name('cp.marketplaces.user-types');
Volt::route('/cp/marketplaces/{marketplace}/user-types/create', 'cp.marketplaces.user-types.create')->name('cp.marketplaces.user-types.create');
Volt::route('/cp/marketplaces/{marketplace}/user-types/1/edit', 'cp.marketplaces.user-types.edit')->name('cp.marketplaces.user-types.edit');

Volt::route('/cp/marketplaces/{marketplace}/user-fields', 'cp.marketplaces.user-fields')->name('cp.marketplaces.user-fields');
Volt::route('/cp/marketplaces/{marketplace}/user-fields/create', 'cp.marketplaces.user-fields.create')->name('cp.marketplaces.user-fields.create');
Volt::route('/cp/marketplaces/{marketplace}/user-fields/1/edit', 'cp.marketplaces.user-fields.edit')->name('cp.marketplaces.user-fields.edit');

Volt::route('/cp/marketplaces/{marketplace}/listing-types', 'cp.marketplaces.listing-types')->name('cp.marketplaces.listing-types');
Volt::route('/cp/marketplaces/{marketplace}/listing-types/create', 'cp.marketplaces.listing-types.create')->name('cp.marketplaces.listing-types.create');
Volt::route('/cp/marketplaces/{marketplace}/listing-types/1/edit', 'cp.marketplaces.listing-types.edit')->name('cp.marketplaces.listing-types.edit');

Volt::route('/cp/marketplaces/{marketplace}/listing-categories', 'cp.marketplaces.listing-categories')->name('cp.marketplaces.listing-categories');
Volt::route('/cp/marketplaces/{marketplace}/listing-categories/create', 'cp.marketplaces.listing-categories.create')->name('cp.marketplaces.listing-categories.create');
Volt::route('/cp/marketplaces/{marketplace}/listing-categories/1/edit', 'cp.marketplaces.listing-categories.edit')->name('cp.marketplaces.listing-categories.edit');

Volt::route('/cp/marketplaces/{marketplace}/listing-fields', 'cp.marketplaces.listing-fields')->name('cp.marketplaces.listing-fields');
Volt::route('/cp/marketplaces/{marketplace}/listing-fields/create', 'cp.marketplaces.listing-fields.create')->name('cp.marketplaces.listing-fields.create');
Volt::route('/cp/marketplaces/{marketplace}/listing-fields/1/edit', 'cp.marketplaces.listing-fields.edit')->name('cp.marketplaces.listing-fields.edit');

Volt::route('/cp/marketplaces/{marketplace}/settings/search', 'cp.marketplaces.settings.search')->name('cp.marketplaces.settings.search');
Volt::route('/cp/marketplaces/{marketplace}/settings/transaction', 'cp.marketplaces.settings.transaction')->name('cp.marketplaces.settings.transaction');
Volt::route('/cp/marketplaces/{marketplace}/settings/commission', 'cp.marketplaces.settings.commission')->name('cp.marketplaces.settings.commission');

Volt::route('/cp/marketplaces/{marketplace}/settings/paymenents', 'cp.marketplaces.settings.payments')->name('cp.marketplaces.settings.payments');
Volt::route('/cp/marketplaces/{marketplace}/settings/maps', 'cp.marketplaces.settings.maps')->name('cp.marketplaces.settings.maps');
Volt::route('/cp/marketplaces/{marketplace}/settings/analytics', 'cp.marketplaces.settings.analytics')->name('cp.marketplaces.settings.analytics');
Volt::route('/cp/marketplaces/{marketplace}/settings/google', 'cp.marketplaces.settings.google')->name('cp.marketplaces.settings.google');
Volt::route('/cp/marketplaces/{marketplace}/settings/zapier', 'cp.marketplaces.settings.zapier')->name('cp.marketplaces.settings.zapier');
