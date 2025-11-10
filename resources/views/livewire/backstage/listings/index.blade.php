<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    #[Computed]
    public function listings()
    {
        $user = Illuminate\Support\Facades\Auth::user();
        $organization = $user?->currentOrganization;
        $marketplace = $organization?->marketplace;
        return $marketplace
            ? $marketplace->listings()->latest()->get()
            : collect();
    }
}; ?>

<div>
    @include('partials.backstage-navbar')

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Title</flux:table.column>
            <flux:table.column>Price</flux:table.column>
            <flux:table.column>Created</flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->listings as $listing)
                <flux:table.row :key="$listing->id">
                    <flux:table.cell>
                        <flux:link :href="route('backstage.listings.show', $listing)">
                            {{ $listing->title }}
                        </flux:link>
                    </flux:table.cell>
                    <flux:table.cell>{{ $listing->price }}</flux:table.cell>
                    <flux:table.cell>{{ $listing->created_at->format('Y-m-d') }}</flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="3">No listings found</flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>
</div>
