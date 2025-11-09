<flux:navbar>
    <flux:navbar.item :href="route('marketplaces.show', $marketplace)">Home</flux:navbar.item>
    <flux:navbar.item :href="route('marketplaces.listings.index', $marketplace)">Listings</flux:navbar.item>
    <flux:navbar.item :href="route('marketplaces.listings.create', $marketplace)">Post a new listings</flux:navbar.item>
    <flux:navbar.item :href="route('marketplaces.inbox.orders', $marketplace)">Inbox</flux:navbar.item>
    <flux:navbar.item :href="route('marketplaces.account.listings', $marketplace)">Profile</flux:navbar.item>
</flux:navbar>
