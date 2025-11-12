<?php

use App\Models\Listing;
use App\Models\Marketplace;
use Livewire\Volt\Component;

new class extends Component
{
    public Marketplace $marketplace;

    public $listings;

    public function mount()
    {
        $this->listings = Listing::where('marketplace_id', $this->marketplace->id)
            ->where('status', 'public')
            ->latest()
            ->get();
    }
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Description</flux:table.column>
            <flux:table.column>Created</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @foreach ($listings as $listing)
                <flux:table.row :key="$listing->id">
                    <flux:table.cell>
                        <flux:link :href="route('marketplaces.listings.show', [$marketplace, $listing])">
                            {{ $listing->title }}
                        </flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $listing->description }}</flux:table.cell>
                    <flux:table.cell>{{ $listing->created_at->diffForHumans() }}</flux:table.cell>
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
