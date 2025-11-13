<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public Listing $listing;

    public string $title = '';

    public string $description = '';

    public function mount()
    {
        $this->title = $this->listing->title;
        $this->description = $this->listing->description;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }

    public function update()
    {
        $this->validate();
        $this->listing->update([
            'title' => $this->title,
            'description' => $this->description,
        ]);
        // Optionally, emit event or redirect
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item :href="route('on-marketplace.listings.edit.details', [$marketplace, $listing])" current wire:navigate>
            Details
        </flux:navbar.item>
        <flux:navbar.item :href="route('on-marketplace.listings.edit.location', [$marketplace, $listing])" wire:navigate>
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

    <flux:heading level="1" size="xl">Details</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="update">
        <flux:field>
            <flux:label badge="Required">Title</flux:label>
            <flux:input wire:model="title" />
            <flux:error name="title" />
        </flux:field>

        <flux:field>
            <flux:label badge="Required">Description</flux:label>
            <flux:textarea wire:model="description"></flux:textarea>
            <flux:error name="description" />
        </flux:field>

        <flux:button type="submit" variant="primary">Next</flux:button>
    </form>
</div>
