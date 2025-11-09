<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;

use App\Models\Listing;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public Marketplace $marketplace;
    public $listings;

    public function mount()
    {
        $this->refreshListings();
    }

    public function openToPublic($listingId)
    {
        $listing = Listing::where('id', $listingId)
            ->where('marketplace_id', $this->marketplace->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $listing->status = 'public';
        $listing->save();
        $this->refreshListings();
    }

    public function closeToPublic($listingId)
    {
        $listing = Listing::where('id', $listingId)
            ->where('marketplace_id', $this->marketplace->id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $listing->status = 'draft';
        $listing->save();
        $this->refreshListings();
    }

    private function refreshListings()
    {
        $this->listings = Listing::where('marketplace_id', $this->marketplace->id)
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }
}; ?>

<div>
    <flux:navbar>
        <flux:navbar.item :href="route('marketplaces.show', $marketplace)">Home</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.listings.create', $marketplace)">Post a new listings</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.inbox.orders', $marketplace)">Inbox</flux:navbar.item>
        <flux:navbar.item :href="route('marketplaces.account.listings', $marketplace)">Profile</flux:navbar.item>
    </flux:navbar>

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column>Status</flux:table.column>
            <flux:table.column>Created</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($listings as $listing)
                <flux:table.row :key="$listing->id">
                    <flux:table.cell>
                        <flux:link :href="route('marketplaces.listings.edit.details', [$marketplace, $listing])">
    {{ $listing->title }}
</flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $listing->description }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge size="sm" color="zinc" inset="top bottom">{{ $listing->status }}</flux:badge>
                        @if ($listing->status === 'public')
                            <flux:button size="xs" color="warning" wire:click="closeToPublic({{ $listing->id }})" class="ml-2">
                                Close to Public
                            </flux:button>
                        @else
                            <flux:button size="xs" color="primary" wire:click="openToPublic({{ $listing->id }})" class="ml-2">
                                Open to Public
                            </flux:button>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>{{ $listing->created_at->diffForHumans() }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
