<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public string $title = '';

    public string $description = '';

    public function rules(): array
    {
        return [
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
        ];
    }

    public function create()
    {
        $this->validate();

        $listing = Listing::create([
            'marketplace_id' => $this->marketplace->id,
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'draft',
        ]);

        return $this->redirectRoute('marketplaces.listings.edit.location', [
            'marketplace' => $this->marketplace,
            'listing' => $listing,
        ], navigate: true);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item current wire:navigate>Details</flux:navbar.item>
        <flux:navbar.item disabled wire:navigate>Location</flux:navbar.item>
        <flux:navbar.item disabled wire:navigate>Pricing</flux:navbar.item>
        <flux:navbar.item disabled wire:navigate>Availability</flux:navbar.item>
        <flux:navbar.item disabled wire:navigate>Photos</flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

    <flux:heading level="1" size="xl">Details</flux:heading>

    <flux:spacer class="my-6" />

    <form class="space-y-6" wire:submit="create">
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

        <flux:button type="submit" variant="primary">Continue</flux:button>
    </form>
</div>
