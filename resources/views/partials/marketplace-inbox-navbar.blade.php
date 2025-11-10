<flux:navbar class="mb-6">
    <flux:navbar.item :href="route('marketplaces.inbox.orders', $marketplace)" :active="request()->routeIs('marketplaces.inbox.orders')">
        Orders
    </flux:navbar.item>
    <flux:navbar.item :href="route('marketplaces.inbox.sales', $marketplace)" :active="request()->routeIs('marketplaces.inbox.sales')">
        Sales
    </flux:navbar.item>
</flux:navbar>
