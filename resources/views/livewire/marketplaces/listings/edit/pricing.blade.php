<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Listing;

new class extends Component {
    public Marketplace $marketplace;
    public Listing $listing;
    public string $price = '';

    public function mount()
    {
        $this->price = (string) $this->listing->price;
    }

    public function rules(): array
    {
        return [
            'price' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function update()
    {
        $this->validate();
        $this->listing->update([
            'price' => $this->price,
        ]);
        // Optionally, emit event or redirect
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <flux:separator class="mb-6" />

    <flux:navbar class="mb-6">
        <flux:navbar.item :href="route('marketplaces.listings.edit.details', [$marketplace, $listing])">
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.location', [$marketplace, $listing])">
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.pricing', [$marketplace, $listing])" active>
            Pricing
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.availability', [$marketplace, $listing])">
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.photos', [$marketplace, $listing])">
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <form class="space-y-6" wire:submit="update">
        <flux:input label='price' wire:model="price" type="number" step="0.01" min="0" />
        <flux:button type="submit">save</flux:button>
    </form>
</div>
