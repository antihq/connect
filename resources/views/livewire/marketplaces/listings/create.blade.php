<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;

new class extends Component {
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
            'listing' => $listing->id,
        ], navigate: true);
    }
}; ?>

<div class="mx-auto max-w-3xl">
    <flux:navbar class="-mb-px">
        <flux:navbar.item current :accent="false">
            Details
        </flux:navbar.item>
        <flux:navbar.item disabled>
            Location
        </flux:navbar.item>
        <flux:navbar.item disabled>
            Pricing
        </flux:navbar.item>
        <flux:navbar.item disabled>
            Availability
        </flux:navbar.item>
        <flux:navbar.item disabled>
            Photos
        </flux:navbar.item>
    </flux:navbar>

    <flux:separator class="mb-6" />

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
