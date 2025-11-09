<?php

use Livewire\Volt\Component;
use App\Models\Marketplace;
use App\Models\Listing;

new class extends Component {
    public Marketplace $marketplace;
    public Listing $listing;
}; ?>

<div>
    @include('partials.marketplace-navbar', ['marketplace' => $marketplace])

    <div class="max-w-2xl mx-auto mt-8 p-6 bg-white rounded shadow">
        <h1 class="text-2xl font-bold mb-2">{{ $listing->title }}</h1>
        <div class="text-zinc-600 mb-4">{{ $listing->description }}</div>
        @if($listing->price)
            <div class="mb-2"><strong>Price:</strong> ${{ number_format($listing->price, 2) }}</div>
        @endif
        @if($listing->address)
            <div class="mb-2"><strong>Address:</strong> {{ $listing->address }}{{ $listing->apt_suite ? ', '.$listing->apt_suite : '' }}</div>
        @endif
        <div class="mb-2 text-sm text-zinc-500">Posted {{ $listing->created_at->diffForHumans() }}</div>
        @if(is_array($listing->photos) && count($listing->photos) > 0)
            <div class="grid grid-cols-2 gap-4 mt-4">
                @foreach($listing->photos as $photo)
                    <img src="/{{ $photo }}" class="rounded shadow object-cover w-full h-40" />
                @endforeach
            </div>
        @endif
    </div>
</div>
