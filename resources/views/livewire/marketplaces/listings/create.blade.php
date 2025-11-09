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

        Listing::create([
            'marketplace_id' => $this->marketplace->id,
            'user_id' => Auth::id(),
            'title' => $this->title,
            'description' => $this->description,
            'status' => 'draft',
        ]);
        // Optionally, reset fields or redirect/emit event
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <flux:separator class="mb-6" />

    <form class="space-y-6" wire:submit="create">
        <flux:input label='title' wire:model="title" />
        <flux:textarea label='description' wire:model="description"></flux:textarea>
        <flux:text>custom fields...</flux:text>
        <flux:button type="submit">next</flux:button>
    </form>
</div>
