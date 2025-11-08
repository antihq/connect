<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div>
    <flux:navbar>
        <flux:navbar.item :href="route('marketplaces.show')">Home</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.create')">Post a new listings</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.inbox.orders')">Inbox</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.profile')">Profile</flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <form class="space-y-6">
        <flux:input label='title' />
        <flux:textarea label='description'></flux:textarea>
        <flux:text>custom fields...</flux:text>
        <flux:button type="submit">next</flux:button>
    </form>
</div>
