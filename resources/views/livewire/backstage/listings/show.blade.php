<?php

use App\Models\Listing;
use Livewire\Volt\Component;

new class extends Component {
    public Listing $listing;

    public function mount(Listing $listing)
    {
        $user = auth()->user();
        $org = $user?->currentOrganization;
        $marketplace = $org?->marketplace;
        if (!$marketplace || $listing->marketplace_id !== $marketplace->id) {
            abort(404);
        }
        $this->listing = $listing;
    }
}; ?>

<div class="max-w-2xl mx-auto mt-10 p-6 bg-white rounded shadow">
    <h1 class="text-2xl font-bold mb-4">{{ $listing->title }}</h1>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Description:</span>
        <span>{{ $listing->description }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Price:</span>
        <span>${{ number_format($listing->price, 2) }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Created at:</span>
        <span>{{ $listing->created_at->format('Y-m-d H:i') }}</span>
    </div>
    <div class="mb-2 text-gray-700">
        <span class="font-semibold">Marketplace:</span>
        <span>{{ $listing->marketplace->name ?? 'N/A' }}</span>
    </div>
    <div class="mt-6 flex items-center gap-4">
        <a href="{{ route('backstage.listings.index') }}" class="text-blue-600 hover:underline">&larr; Back to Listings</a>
        <a href="{{ route('backstage.listings.edit', $listing) }}" class="text-white bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded transition">Edit Listing</a>
    </div>
</div>
