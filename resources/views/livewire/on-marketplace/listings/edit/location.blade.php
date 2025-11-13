<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Listing $listing;

    public string $address = '';

    public string $apt_suite = '';

    public function mount()
    {
        $this->address = $this->listing->address ?? '';
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

        return $this->redirectRoute('on-marketplace.listings.edit.pricing', [
            'marketplace' => $this->marketplace,
            'listing' => $this->listing,
        ], navigate: true);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('on-marketplace.listings.edit.details', [$marketplace, $listing])" wire:navigate>
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.location', [$marketplace, $listing])" current wire:navigate>
            Location
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.pricing', [$marketplace, $listing])" wire:navigate>
            Pricing
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.availability', [$marketplace, $listing])" wire:navigate>
            Availability
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.photos', [$marketplace, $listing])" wire:navigate>
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Location</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="update">
        <flux:field>
            <flux:label badge="Required">Address</flux:label>
            <flux:input wire:model="address" />
            <flux:error name="address" />
        </flux:field>

        <flux:field>
            <flux:label badge="Optional">Apt, suite, building #</flux:label>
            <flux:input wire:model="apt_suite" />
            <flux:error name="apt_suite" />
        </flux:field>

        <flux:button type="submit" variant="primary">Next</flux:button>
    </form>
</div>
