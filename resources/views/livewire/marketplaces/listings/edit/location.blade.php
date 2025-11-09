<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Listing;

new class extends Component {
    public Marketplace $marketplace;
    public Listing $listing;
    public string $address = '';
    public string $apt_suite = '';

    public function mount()
    {
        $this->address = $this->listing->address;
        $this->apt_suite = $this->listing->apt_suite ?? '';
    }

    public function rules(): array
    {
        return [
            'address' => ['required', 'string'],
            'apt_suite' => ['nullable', 'string'],
        ];
    }

    public function update()
    {
        $this->validate();
        $this->listing->update([
            'address' => $this->address,
            'apt_suite' => $this->apt_suite,
        ]);
        // Optionally, emit event or redirect
    }
}; ?>

<div>
    <flux:navbar>
        <flux:navbar.item :href="route('marketplaces.show', $marketplace)">Home</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.create', $marketplace)">Post a new listings</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.inbox.orders', $marketplace)">Inbox</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.account.listings', $marketplace)">Profile</flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <form class="space-y-6" wire:submit="update">
        <flux:input label='address' wire:model="address" />
        <flux:input label='apt, suite, building #' wire:model="apt_suite" />
        <flux:button type="submit">save</flux:button>
    </form>
</div>
