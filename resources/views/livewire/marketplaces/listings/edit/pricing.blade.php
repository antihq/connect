<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

new class extends Component
{
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

        return $this->redirectRoute('marketplaces.listings.edit.availability', [
            'marketplace' => $this->marketplace,
            'listing' => $this->listing,
        ], navigate: true);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('marketplaces.listings.edit.details', [$marketplace, $listing])">
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.location', [$marketplace, $listing])">
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.pricing', [$marketplace, $listing])" current>
            Pricing
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.availability', [$marketplace, $listing])">
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.edit.photos', [$marketplace, $listing])">
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Pricing</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="update">
        <flux:field>
            <flux:label badge="Required">Price per day</flux:label>
            <flux:input wire:model="price" type="number" step="0.01" min="0" />
            <flux:error name="price" />
        </flux:field>
        <flux:button type="submit" variant="primary">Next</flux:button>
    </form>
</div>
