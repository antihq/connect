<?php

use Livewire\Volt\Volt;

Volt::route('/marketplace/1/', 'marketplaces.show');
Volt::route('/marketplace/1/search', 'marketplaces.search');

Volt::route('/marketplace/1/pages/about', 'marketplaces.pages.about');
Volt::route('/marketplace/1/pages/terms', 'marketplaces.pages.terms');
Volt::route('/marketplace/1/pages/privacy', 'marketplaces.pages.privacy');

Volt::route('/marketplace/1/inbox/orders', 'marketplaces.inbox.orders');
Volt::route('/marketplace/1/inbox/sales', 'marketplaces.inbox.sales');

Volt::route('/marketplace/1/sales', 'marketplaces.sales.index');
Volt::route('/marketplace/1/sales/1', 'marketplaces.sales.show');

Volt::route('/marketplace/1/orders', 'marketplaces.orders.index');
Volt::route('/marketplace/1/orders/1', 'marketplaces.orders.show');

Volt::route('/marketplace/1/test/listings', 'marketplaces.listings.index');
Volt::route('/marketplace/1/test/listings/create', 'marketplaces.listings.create');
Volt::route('/marketplace/1/test/listings/1', 'marketplaces.listings.show');

Volt::route('/marketplace/1/test/listings/1/edit/details', 'marketplaces.listings.edit.details');
Volt::route('/marketplace/1/test/listings/1/edit/location', 'marketplaces.listings.edit.location');
Volt::route('/marketplace/1/test/listings/1/edit/pricing', 'marketplaces.listings.edit.pricing');
Volt::route('/marketplace/1/test/listings/1/edit/availability', 'marketplaces.listings.edit.availability');
Volt::route('/marketplace/1/test/listings/1/edit/photos', 'marketplaces.listings.edit.photos');

Volt::route('/marketplace/1/test/account/profile', 'marketplaces.profile');

Volt::route('/marketplace/1/test/account/settings/contact', 'marketplaces.account.settings.contact');
Volt::route('/marketplace/1/test/account/settings/password', 'marketplaces.account.settings.password');
Volt::route('/marketplace/1/test/account/settings/payout', 'marketplaces.account.settings.payout');
Volt::route('/marketplace/1/test/account/settings/payment', 'marketplaces.account.settings.payment');

Volt::route('/marketplace/1/test/users/1', 'marketplaces.users.show');

Volt::route('/marketplace/1/test/login', 'marketplaces.login');
Volt::route('/marketplace/1/test/register', 'marketplaces.register');

Volt::route('/cp', 'cp');

Volt::route('/cp/users', 'cp.users.index');
Volt::route('/cp/users/1', 'cp.users.show');
Volt::route('/cp/users/1/edit', 'cp.users.edit');

Volt::route('/cp/listings', 'cp.listings.index');
Volt::route('/cp/listings/1', 'cp.listings.show');
Volt::route('/cp/listings/1/edit', 'cp.listings.edit');

Volt::route('/cp/transactions', 'cp.transactions.index');
Volt::route('/cp/transactions/1', 'cp.transactions.show');

Volt::route('/cp/reviews', 'cp.reviews.index');
Volt::route('/cp/reviews/1', 'cp.reviews.show');
Volt::route('/cp/reviews/1/edit', 'cp.reviews.edit');

Volt::route('/cp/marketplaces/1/settings/name', 'cp.marketplaces.settings.name');
Volt::route('/cp/marketplaces/1/settings/domain', 'cp.marketplaces.settings.domain');
Volt::route('/cp/marketplaces/1/settings/domain', 'cp.marketplaces.settings.domain');
Volt::route('/cp/marketplaces/1/settings/email', 'cp.marketplaces.settings.email');
Volt::route('/cp/marketplaces/1/settings/localization', 'cp.marketplaces.settings.localization');
Volt::route('/cp/marketplaces/1/settings/access', 'cp.marketplaces.settings.access');

Volt::route('/cp/marketplaces/1/pages', 'cp.marketplaces.pages.index');
Volt::route('/cp/marketplaces/1/pages/create', 'cp.marketplaces.pages.create');
Volt::route('/cp/marketplaces/1/pages/1/edit', 'cp.marketplaces.pages.edit');

Volt::route('/cp/marketplaces/1/settings/top-bar', 'cp.marketplaces.settings.top-bar');
Volt::route('/cp/marketplaces/1/top-bar-links/create', 'cp.marketplaces.top-bar-links.create');
Volt::route('/cp/marketplaces/1/top-bar-links/1/edit', 'cp.marketplaces.top-bar-links.edit');

Volt::route('/cp/marketplaces/1/settings/footer', 'cp.marketplaces.settings.footer');
Volt::route('/cp/marketplaces/1/social-media-links/create', 'cp.marketplaces.social-media-links.create');
Volt::route('/cp/marketplaces/1/social-media-links/1/edit', 'cp.marketplaces.social-media-links.edit');
Volt::route('/cp/marketplaces/1/content-blocks/create', 'cp.marketplaces.content-blocks.create');
Volt::route('/cp/marketplaces/1/content-blocks/1/edit', 'cp.marketplaces.content-blocks.edit');

Volt::route('/cp/marketplaces/1/settings/texts', 'cp.marketplaces.settings.texts');

Volt::route('/cp/marketplaces/1/settings/design/branding', 'cp.marketplaces.settings.design.branding');
Volt::route('/cp/marketplaces/1/settings/design/layout', 'cp.marketplaces.settings.design.layout');

Volt::route('/cp/marketplaces/1/user-types', 'cp.marketplaces.user-types');
Volt::route('/cp/marketplaces/1/user-types/create', 'cp.marketplaces.user-types.create');
Volt::route('/cp/marketplaces/1/user-types/1/edit', 'cp.marketplaces.user-types.edit');

Volt::route('/cp/marketplaces/1/user-fields', 'cp.marketplaces.user-fields');
Volt::route('/cp/marketplaces/1/user-fields/create', 'cp.marketplaces.user-fields.create');
Volt::route('/cp/marketplaces/1/user-fields/1/edit', 'cp.marketplaces.user-fields.edit');

Volt::route('/cp/marketplaces/1/listing-types', 'cp.marketplaces.listing-types');
Volt::route('/cp/marketplaces/1/listing-types/create', 'cp.marketplaces.listing-types.create');
Volt::route('/cp/marketplaces/1/listing-types/1/edit', 'cp.marketplaces.listing-types.edit');

Volt::route('/cp/marketplaces/1/listing-categories', 'cp.marketplaces.listing-categories');
Volt::route('/cp/marketplaces/1/listing-categories/create', 'cp.marketplaces.listing-categories.create');
Volt::route('/cp/marketplaces/1/listing-categories/1/edit', 'cp.marketplaces.listing-categories.edit');

Volt::route('/cp/marketplaces/1/listing-fields', 'cp.marketplaces.listing-fields');
Volt::route('/cp/marketplaces/1/listing-fields/create', 'cp.marketplaces.listing-fields.create');
Volt::route('/cp/marketplaces/1/listing-fields/1/edit', 'cp.marketplaces.listing-fields.edit');

Volt::route('/cp/marketplaces/1/settings/search', 'cp.marketplaces.settings.search');
Volt::route('/cp/marketplaces/1/settings/transaction', 'cp.marketplaces.settings.transaction');
Volt::route('/cp/marketplaces/1/settings/commission', 'cp.marketplaces.settings.commission');

Volt::route('/cp/marketplaces/1/settings/paymenents', 'cp.marketplaces.settings.payments');
Volt::route('/cp/marketplaces/1/settings/maps', 'cp.marketplaces.settings.maps');
Volt::route('/cp/marketplaces/1/settings/analytics', 'cp.marketplaces.settings.analytics');
Volt::route('/cp/marketplaces/1/settings/google', 'cp.marketplaces.settings.google');
Volt::route('/cp/marketplaces/1/settings/zapier', 'cp.marketplaces.settings.zapier');
